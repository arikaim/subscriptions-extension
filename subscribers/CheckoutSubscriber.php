<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Subscriptions\Subscribers;

use Arikaim\Core\Events\EventSubscriber;
use Arikaim\Core\Interfaces\Events\EventSubscriberInterface;
use Arikaim\Core\Arikaim;
use Arikaim\Core\Db\Model;

/**
 * Execute checkout actions 
*/
class CheckoutSubscriber extends EventSubscriber implements EventSubscriberInterface
{
    /**
     * Constructor
     */
    public function __construct() 
    {
        $this->subscribe('checkout.create','create');
        $this->subscribe('checkout.token.update','update');
        $this->subscribe('checkout.success','success');
        $this->subscribe('checkout.notify','notify');
    }

    /**
     * Run checkout.create action
     *
     * @param EventInterface $event
     * @return mixed
     */
    public function create($event)
    {
        $token = $event->getParameter('token'); 
        $slug = $event->getParameter('order_id'); 
        $billingType = $event->getParameter('options','monthly'); 
        $driverName = $event->getParameter('checkout_driver');

        $plan = Model::SubscriptionPlans('subscriptions')->findPlan($slug);

        if (\is_object($plan) == false) {
            return [null];
        }
        // check user 
        $user = Model::Users()->findById($event->getParameter('user_id'));
        if (\is_object($user) == false) {
            return [null];
        }

        $subscription = Model::Subscriptions('subscriptions')->createSubscription($user->id,$plan->id,$billingType,$token,$driverName);
        if (\is_object($subscription) == false) {
            return [null];
        }

        $checkoutData = Arikaim::get('content')->createItem([
            'amount'          => \number_format($plan->getPrice($billingType),2),
            'currency'        => $plan->currency->code,
            'order_id'        => $subscription->id,
            'extension'       => $event->getParameter('extension'),
            'checkout_driver' => $driverName,
            'token'           => $token
        ],'checkout');

        return $checkoutData;
    }

    /**
     * Run checkout.notify action
     *
     * @param EventInterface $event
     * @return void
     */
    public function notify($event)
    {
        $data = $event->getParameters(); 
    }

    /**
     * Run checkout.success action
     *
     * @param EventInterface $event
     * @return bool
     */
    public function success($event)
    {      
        $amount = (float)$event->getParameter('amount');       
        $subscriptionId = $event->getParameter('order_id');
        if ($amount == 0 || empty($amount) == true) {
            return false;
        }

        $subscription = Model::Subscriptions('subscriptions')->findById($subscriptionId);
        if (\is_object($subscription) == false) {
            return false;
        }

        return $subscription->activateSubscription();
    }

    /**
     * Run checkout.token.update action
     *
     * @param EventInterface $event
     * @return bool
     */
    public function update($event)
    {
        $token = $event->getParameter('token'); 
        $checkoutDriver = $event->getParameter('checkout_driver');
        $subscriptionId = $event->getParameter('order_id'); 
        if (empty($token) == true || empty($checkoutDriver) == true) {
            return false;
        }

        $subscription = Model::Subscriptions('subscriptions')->findById($subscriptionId);
        if (\is_object($subscription) == false) {
            return false;
        }

        return (\is_object($subscription) == true) ? $subscription->updateCheckoutToken($token,$checkoutDriver) : false;           
    }

}
