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
     * Return true if user is subscribed
     *    
     * @param int $userId
     * @param string|null $planSlug
     * @return boolean
     */
    public function hasSubscription(int $userId, ?string $planSlug = null): bool
    {
        return (bool)Model::Subscriptions('subscriptions')->isSusbscribed($userId,$planSlug);
    }

    /**
     * Return true if user is subscribed
     *    
     * @param int $userId 
     * @return Model|null
     */
    public function getSubscriptionPlan(int $userId)
    {
       $subscription = Model::Subscriptions('subscriptions')->getSubscription($userId);
       if (empty($subscription) == true) {
           return null;
       }

       return $subscription->plan->first();
    }

    /**
     * Check if plan feature exist
     *
     * @param integer $userId
     * @param string $key
     * @return boolean
     */
    public function hasPlanFeature(int $userId, string $key): bool
    {
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
     * @param integer $userId
     * @param string $key
     * @return mixed
     */
    public function getPlanFeatureValue(int $userId, string $key)
    {
        $plan = $this->getSubscriptionPlan($userId);
        if (empty($plan) == true) {
            return null;
        }
        $feature = $plan->features->where('key','=',$key)->first();

        return (empty($feature) == true) ? null : $feature->item_value;         
    }
}
