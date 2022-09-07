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
use Arikaim\Extensions\Subscriptions\Classes\Subscriptions;

/**
 * Subscription plans control panel api controler
*/
class PlansControlPanel extends ControlPanelApiController
{
    use Status;

    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('subscriptions::admin.messages');
        $this->setModelClass('SubscriptionPlans');
        $this->setExtensionName('subscriptions');
    }

    /**
     * Create subscription plan
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function createController($request, $response, $data) 
    {       
        $data->validate(true);   

        $uuid = $data->get('uuid');
        $billingType = $data->get('billing_type');
        $driverName = $this->get('options')->get('subscriptions.driver');
        $driver = $this->get('driver')->create($driverName);

        $apiResponse = Subscriptions::createPlan($driver,$uuid,$billingType);
        if ($apiResponse->hasError() == true) {
            $this->error($apiResponse->getError());
            return false;
        }

        $result = $apiResponse->getResult();
        $planId = $result['id'] ?? null;

        $this->setResponse(!empty($planId),function() use($uuid,$planId) {                  
            $this
                ->message('plan.create')
                ->field('plan_id',$planId)
                ->field('uuid',$uuid);             
        },'errors.plan.create');              
    }

    /**
     * Activate plan
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function activateController($request, $response, $data) 
    {       
        $data->validate(true);    

        $planId = $data->get('plan_id');
        $driverName = $this->get('options')->get('subscriptions.driver');
        $driver = $this->get('driver')->create($driverName);

        $result = Subscriptions::activatePlan($driver,$planId);
        
        $this->setResponse($result,function() use($planId) {                  
            $this
                ->message('plan.activate')
                ->field('status','ACTIVE')
                ->field('plan_id',$planId);          
        },'errors.plan.activate');     
    }

    /**
     * Bind provider plan with subscription plan
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function bindController($request, $response, $data) 
    {       
        $data->validate(true);   

        $uuid = $data->get('uuid');
        $providerPlanId = $data->get('plan_id');
        $billingType = $data->get('billing_type');
        
        $model = Model::create('SubscriptionPlans','subscriptions');
        $plan = $model->findById($uuid);
        if ($plan == null) {
            $this->error('errors.plan.id','Not valid plan id');
            return false;
        }
        
        $result = $plan->updatePlanId($billingType,$providerPlanId);
        
        $this->setResponse($result,function() use($uuid,$providerPlanId) {                  
            $this
                ->message('plan.bind')
                ->field('plan_id',$providerPlanId)
                ->field('uuid',$uuid);             
        },'errors.plan.bind');              
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
        $data->validate(true);  

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
        $data
            ->addFilter('annual_price','ToFloat')
            ->addFilter('monthly_price','ToFloat')
            ->filterAndValidate(true);   
         
        $uuid = $data->get('uuid',null);
        $model = Model::create('SubscriptionPlans','subscriptions')->findById($uuid);
        if ($model == null) {
            $this->error('errors.plan.id','Not valid plane id.');
            return false;
        }
        
        $result = $model->update($data->toArray());

        $this->setResponse($result,function() use($uuid) {                  
            $this
                ->message('plan.update')
                ->field('uuid',$uuid);             
        },'errors.plan.update');              
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
            $subscriptions = Model::create('Subscriptions','subscriptions');
            $plan = Model::create('SubscriptionPlans','subscriptions')->findById($uuid);

            if (\is_object($plan) == false) {
                $this->error('errors.plan.id');
                return false;
            }
            
            if ($subscriptions->hasSubscriptions($plan->id) == true) {
                $this->error('errors.plan.subscriptions');
                return false;
            }
            
            $result = $plan->deletePlan();

            $this->setResponse($result,function() use($uuid) {                  
                $this
                    ->message('plan.delete')
                    ->field('uuid',$uuid);             
            },'errors.plan.delete');              
        });
        $data->validate();                
    }
}
