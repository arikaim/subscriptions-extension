<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Subscriptions\Classes;

use Arikaim\Core\Db\Model;

/**
 * Subscriptions  
*/
class Subscriptions
{
    /**
     * Create subscription
     *
     * @param object $apiDriver
     * @param string $planId
     * @param string|null $title
     * @param string|null $description
     * @return mixed
     */
    public static function createSubscription($apiDriver, $planId, $title = null, $description = null)
    {
        $result = $apiDriver->subscription()->create($planId,$title,$description);

        return $result;
    }

    /**
     * Create subscription plan
     *
     * @param object $apiDriver
     * @param string|int $planId
     * @param string $billingType
     * @return mixed
     */
    public static function createPlan($apiDriver, $planId, $billingType = 'monthly') 
    { 
        // Subscription Plan
        $plan = Model::SubscriptionPlans('subscriptions')->findById($planId);
        if (\is_object($plan) == false) {
            return false;
        }
      
        return $apiDriver->plan()->create(
            $plan->title,
            $plan->description,
            $plan->getPrice($billingType),
            $plan->currency->code,
            $billingType
        );      
    }

    /**
     * Activate plan
     *
     * @param object $apiDriver
     * @param string $planId
     * @return bool
     */
    public static function activatePlan($apiDriver, $planId)
    {
        $result = $apiDriver->plan()->update($planId,[
            'state' => 'ACTIVE'
        ]);

        return $result->hasSuccess();       
    }
}
