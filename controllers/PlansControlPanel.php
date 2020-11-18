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

/**
 * Subscription plans control panel api controler
*/
class PlansControlPanel extends ControlPanelApiController
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
        $this->setModelClass('SubscriptionPlans');
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
     * Add subscription plan
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function addController($request, $response, $data) 
    {       
        $this->onDataValid(function($data) {             
            $model = Model::create('SubscriptionPlans','subscriptions');

            if ($model->hasPlan($data['title']) == true) {
                $this->error('errors.plan.exist');
                return false;
            }
            
            $plan = $model->create($data->toArray());

            $this->setResponse(\is_object($plan),function() use($plan) {                  
                $this
                    ->message('plan.add')
                    ->field('uuid',$plan->uuid);             
            },'errors.plan.add');              
        });
        $data->validate();                
    }

    /**
     * Update subscription plan
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function updateController($request, $response, $data) 
    {       
        $this->onDataValid(function($data) {             
            $uuid = $data->get('uuid',null);
            $model = Model::create('SubscriptionPlans','subscriptions')->findById($uuid);

            if (\is_object($model) == false) {
                $this->error('errors.plan.id');
                return false;
            }
            
            $result = $model->update($data->toArray());

            $this->setResponse($result,function() use($uuid) {                  
                $this
                    ->message('plan.update')
                    ->field('uuid',$uuid);             
            },'errors.plan.update');              
        });
        $data->validate();                
    }

    /**
     * Delete subscription plan
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function deleteController($request, $response, $data) 
    {       
        $this->onDataValid(function($data) {             
            $uuid = $data->get('uuid',null);
            $model = Model::create('SubscriptionPlans','subscriptions')->findById($uuid);

            if (\is_object($model) == false) {
                $this->error('errors.plan.id');
                return false;
            }
            
            $result = $model->deletePlan();

            $this->setResponse($result,function() use($uuid) {                  
                $this
                    ->message('plan.delete')
                    ->field('uuid',$uuid);             
            },'errors.plan.delete');              
        });
        $data->validate();                
    }
}
