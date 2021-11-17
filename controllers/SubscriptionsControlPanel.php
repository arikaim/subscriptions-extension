<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Subscriptions\Controllers;

use Arikaim\Core\Controllers\ControlPanelApiController;
use Arikaim\Core\Controllers\Traits\Status;
use Arikaim\Core\Db\Model;
use Arikaim\Core\Utils\Uuid;
use Arikaim\Extensions\Subscriptions\Classes\Subscriptions;

/**
 * Subscriptions control panel api controler
*/
class SubscriptionsControlPanel extends ControlPanelApiController
{
    use Status;

    /**
     * Constructor
     * 
     * @param Container|null $container
    */
    public function __construct($container = null) 
    {
        parent::__construct($container);
        $this->setModelClass('Subscriptions');
        $this->setExtensionName('subscriptions');
    }

    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('subscriptions::admin.messages');
    }

    /**
     * Create subscription
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function addController($request, $response, $data) 
    {       
        $this->onDataValid(function($data) {   
            $userId = $data->get('user_id');
            $planId = $data->get('plan_id');
            $billingType = $data->get('billing_type','monthly');
            $model = Model::Subscriptions('subscriptions');
            $subscription = $model->getSubscription($userId);

            if (\is_object($subscription) == true) {
                $result = $subscription->setStatus(1);
            } else {
                $token = Uuid::create();
                $result = $model->registerSubscription($userId,$planId,$billingType,$token,'admin');
                $subscription = $model->getSubscription($userId);
                if (\is_object($subscription) == true) {
                    $subscription->setStatus(1);
                }
            }

            $this->setResponse($result,function() use($subscription) {                  
                $this
                    ->message('subscription.add')
                    ->field('plan_id',$subscription->plan_id)
                    ->field('uuid',$subscription->uuid);             
            },'errors.subscription.add');              
        });
        $data->validate();                
    }
}
