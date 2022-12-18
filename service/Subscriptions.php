<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Subscriptions\Service;

use Arikaim\Core\Db\Model;
use Arikaim\Core\Service\Service;
use Arikaim\Core\Service\ServiceInterface;
use Arikaim\Core\Arikaim;
use Arikaim\Core\Utils\Uuid;

/**
 * Subscriptions service class
*/
class Subscriptions extends Service implements ServiceInterface
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setServiceName('subscriptions');
    }

    /**
     * Add subscription
     *
     * @param integer $usedrId
     * @param integer $planId
     * @param string|null $expirePeriod
     * @param string|null $billingType
     * @return Model|false;
     */
    public function add(int $userId, int $planId, ?string $expirePeriod, ?string $billingType = 'monthly')
    {
        $model = Model::Subscriptions('subscriptions');
        $subscription = $model->getSubscription($userId);

        if (\is_object($subscription) == true) {
            $subscription->setStatus(1);
            $subscription->setExpirePeriod($expirePeriod);
            $subscription->update([
                'plan_id' => $planId
            ]);
        } else {
            $token = Uuid::create();
            $model->registerSubscription($userId,$planId,$billingType,$token,'admin');
            $subscription = $model->getSubscription($userId);
            if (\is_object($subscription) == true) {
                $subscription->setStatus(1);
                $subscription->setExpirePeriod($expirePeriod);
            }
        }

        return (\is_object($subscription) == true) ? $subscription : false;
    }

    /**
     * Cancel subscription
     *
     * @param SubscriptionsProviderInteface $apiDriver
     * @param integer $userId
     * @return boolean
     */
    public static function cancel(int $userId): bool
    {
        $subscription = Model::Subscriptions('subscriptions')->getSubscription($userId);
        if (\is_object($subscription) == false) {
            return false;
        }
        if ($subscription->checkout_driver == 'admin') {
            $subscription->setStatus(6); // Canceled created by admin subscription
            return true;
        }

        if (empty($subscription->subscription_id) == true) {
            return false;
        }

        $apiDriver = Arikaim::get('driver')->create($subscription->checkout_driver);
        if (\is_object($apiDriver) == false) {
            return false;
        }
     
        $apiResponse = $apiDriver->subscription()->cancel($subscription->subscription_id);

        if ($apiResponse->hasError() == false) {
            $subscription->setStatus(6); // Canceled
            return true;
        }

        return false;
    }

    /**
     * Get subscription plan model
     *
     * @param mixed $slug
     * @return Model|null
     */
    public function getPlan($slug)
    {
        return Model::SubscriptionPlans('subscriptions')->findPlan($slug);
    } 
    
    /**
     * Return true if user is subscribed
     *    
     * @param int|null $userId
     * @param string|array|null $plans
     * @return boolean
     */
    public function hasSubscription(?int $userId, $plans = null): bool
    {
        if (empty($userId) == true) {
            return false;
        }

        $model = Model::Subscriptions('subscriptions');
        if (\is_array($plans) == true) {
            foreach($plans as $plan) {
                if ($model->isSusbscribed($userId,$plan) == true) {
                    return true;
                }
            }
            return false;
        }

        return $model->isSusbscribed($userId,$plans);
    }

    /**
     * Return plan mopdel if user is subscribed
     *    
     * @param int|null $userId 
     * @return Model|null
     */
    public function getSubscriptionPlan(?int $userId): ?object
    {
        if (empty($userId) == true) {
            return null;
        }
        $subscription = Model::Subscriptions('subscriptions')->getSubscription($userId);
        if ($subscription == null) {
            return null;
        }

        if ($subscription->status != $subscription->ACTIVE()) {
            // for canceled or disabled get free plan
            return Model::SubscriptionPlans('subscriptions')->getFreePlan();
        }

        return $subscription->plan;                   
    }

    /**
     * Check if plan feature exist
     *
     * @param integer|null $userId
     * @param string $key
     * @return boolean
     */
    public function hasPlanFeature(?int $userId, string $key): bool
    {
        if (empty($userId) == true) {
            return false;
        }

        $plan = $this->getSubscriptionPlan($userId);
        if ($plan == null) {
            return false;
        }
        $feature = $plan->features->where('key','=',$key)->first();

        return ($feature != null);
    }

    /**
     * Check plan feature quota
     *
     * @param integer|null $userId
     * @param string       $key
     * @param mixed        $value
     * @return boolean
     */
    public function checkPlanFeatureQuota(?int $userId, string $key, $value): bool
    {
        if (empty($userId) == true) {
            return false;
        }

        $featureValue = $this->getPlanFeatureValue($userId,$key);
        if ($featureValue === null) {
            return false;
        }

        if ($featureValue == -1) {
            // unlimited
            return true;
        }

        return !($value >= $featureValue);
    }

    /**
     * Get plan feature item value
     *
     * @param integer|null $userId
     * @param string $key
     * @return mixed
     */
    public function getPlanFeatureValue(?int $userId, string $key)
    {
        if (empty($userId) == true) {
            return null;
        }

        $plan = $this->getSubscriptionPlan($userId);
        if ($plan == null) {
            return null;
        }
        $feature = $plan->features->where('key','=',$key)->first();

        return ($feature == null) ? null : $feature->item_value;         
    }

    /**
     * Save feature type
     *
     * @param string      $key
     * @param string      $title
     * @param integer     $itemValue
     * @param string|null $description
     * @return object|null
     */
    public function saveFeatureType(string $key, string $title, ?string $description = null, $itemValue = 0): ?object
    {
        return Model::PlanFeatureTypes('subscriptions')->saveFeatureType($key,$title,$description,$itemValue);
    }
}
