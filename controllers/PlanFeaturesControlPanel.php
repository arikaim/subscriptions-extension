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
use Arikaim\Core\Db\Model;

/**
 * Subscription plan features control panel api controler
*/
class PlanFeaturesControlPanel extends ControlPanelApiController
{
    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('subscriptions::admin.messages');
        $this->setModelClass('SubscriptionPlanFeatures');
        $this->setExtensionName('subscriptions');
    }

    /**
     * Add plan features
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function addController($request, $response, $data) 
    {       
        $this->onDataValid(function($data) {             
            $model = Model::create('SubscriptionPlanFeatures','subscriptions');
 
            if ($model->hasFeature($data['key'],$data['plan_id']) == true) {
                $this->error('errors.feature.exist');
                return false;
            }

            $plan = $model->create($data->toArray());

            $this->setResponse(\is_object($plan),function() use($plan) {                  
                $this
                    ->message('feature.add')
                    ->field('uuid',$plan->uuid);             
            },'errors.feature.add');              
        });
        $data->validate();                
    }

    /**
     * Update plan features
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
            $model = Model::create('SubscriptionPlanFeatures','subscriptions')->findById($uuid);

            if (\is_object($model) == false) {
                $this->error('errors.feature.id');
                return false;
            }
            
            $result = $model->update($data->toArray());

            $this->setResponse($result,function() use($uuid) {                  
                $this
                    ->message('feature.update')
                    ->field('uuid',$uuid);             
            },'errors.feature.update');              
        });
        $data->validate();                
    }

    /**
     * Delete plan feature
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
            $model = Model::create('SubscriptionPlanFeatures','subscriptions')->findById($uuid);

            if (\is_object($model) == false) {
                $this->error('errors.feature.id');
                return false;
            }
            
            $result = (bool)$model->delete();

            $this->setResponse($result,function() use($uuid) {                  
                $this
                    ->message('feature.delete')
                    ->field('uuid',$uuid);             
            },'errors.feature.delete');              
        });
        $data->validate();                
    }
}
