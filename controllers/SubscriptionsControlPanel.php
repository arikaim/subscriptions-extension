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
            $userId = (int)$data->get('user_id');
            $planId = (int)$data->get('plan_id');
            $expirePeriod = (string)$data->get('expire_period');
            $billingType = (string)$data->get('billing_type','monthly');

            $subscription = $this->get('subscriptions')->add($userId,$planId,$expirePeriod,$billingType);

            $this->setResponse(($subscription !== false),function() use($subscription) {                  
                $this
                    ->message('subscription.add')
                    ->field('plan_id',$subscription->plan_id)
                    ->field('uuid',$subscription->uuid);             
            },'errors.subscription.add');              
        });
        $data->validate();                
    }
}
