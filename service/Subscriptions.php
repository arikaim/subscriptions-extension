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
     * Return true if user is subscribed
     *    
     * @param int|null $userId 
     * @return Model|null
     */
    public function getSubscriptionPlan(?int $userId)
    {
        if (empty($userId) == true) {
            return null;
        }

        $subscription = Model::Subscriptions('subscriptions')->getSubscription($userId);
        if (empty($subscription) == true) {
            return null;
        }

        return $subscription->plan->first();
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
        if (empty($plan) == true) {
            return false;
        }
        $feature = $plan->features->where('key','=',$key)->first();

        return \is_object($feature);
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
        if (empty($plan) == true) {
            return null;
        }
        $feature = $plan->features->where('key','=',$key)->first();

        return (empty($feature) == true) ? null : $feature->item_value;         
    }
}
