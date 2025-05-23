<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Subscriptions;

use Arikaim\Core\Extension\Extension;

/**
 * Subscriptions extension
*/
class Subscriptions extends Extension
{
    /**
     * Install extension routes, events, jobs
     *
     * @return void
    */
    public function install()
    {        
        // Control Panel  
        // Subscriptions
        $this->addApiRoute('POST','/api/admin/subscriptions/add','SubscriptionsControlPanel','add','session'); 
        $this->addApiRoute('PUT','/api/admin/subscriptions/status','SubscriptionsControlPanel','setStatus','session');  
        // Plans
        $this->addApiRoute('POST','/api/subscriptions/admin/plans/add','PlansControlPanel','add','session');   
        $this->addApiRoute('PUT','/api/subscriptions/admin/plans/update','PlansControlPanel','update','session'); 
        $this->addApiRoute('DELETE','/api/subscriptions/admin/plans/delete/{uuid}','PlansControlPanel','delete','session'); 
        $this->addApiRoute('PUT','/api/subscriptions/admin/plans/status','PlansControlPanel','setStatus','session'); 
        // Provider 
        $this->addApiRoute('PUT','/api/subscriptions/admin/plan/bind','PlansControlPanel','bind','session');   
        $this->addApiRoute('PUT','/api/subscriptions/admin/plan/activate','PlansControlPanel','activate','session');
        $this->addApiRoute('POST','/api/subscriptions/admin/plan/create','PlansControlPanel','create','session');   
        // Feature types
        $this->addApiRoute('POST','/api/subscriptions/admin/feature/type/add','FeatureTypesControlPanel','create','session');   
        $this->addApiRoute('PUT','/api/subscriptions/admin/feature/type/update','FeatureTypesControlPanel','update','session'); 
        $this->addApiRoute('DELETE','/api/subscriptions/admin/feature/type/delete/{uuid}','FeatureTypesControlPanel','delete','session'); 
        // Plan Features
        $this->addApiRoute('POST','/api/subscriptions/admin/plan/feature/add','PlanFeaturesControlPanel','add','session');   
        $this->addApiRoute('PUT','/api/subscriptions/admin/plan/feature/update','PlanFeaturesControlPanel','update','session'); 
        $this->addApiRoute('DELETE','/api/subscriptions/admin/plan/feature/delete/{uuid}','PlanFeaturesControlPanel','delete','session'); 
        // Pages
        $this->addPageRoute('/subscription[/]','SubscriptionPages','showPlans','subscriptions>subscription',null,'subscription.page');
        $this->addPageRoute('/subscription/signup/{plan}/{billing}','SubscriptionPages','signup','subscriptions>subscription.signup');
        // checkout
        $this->addPageRoute('/subscription/signup/checkout/{driver_name}/{plan}/{billing}','SubscriptionPages','signupCheckout','subscriptions>subscription.signup');
        $this->addPageRoute('/subscription/create/{plan}/{billing}[/{user}[/{language:[a-z]{2}}/]]','SubscriptionPages',
            'create','subscriptions>subscription',null,
            'subscription.create.page',false
        );  
        $this->addPageRoute('/subscription/success/','SubscriptionPages','success','subscriptions>subscription.success');
        $this->addPageRoute('/subscription/cancel/','SubscriptionPages','cancel','subscriptions>subscription.cancel');   
        // API
        $this->addApiRoute('POST','/api/subscription/notify','SubscriptionsApi','notify',null); 
        $this->addApiRoute('DELETE','/api/subscription/cancel','SubscriptionsApi','cancel','session'); 
        // Events
        $this->registerEvent('subscriptions.create','Create subscription');        
        $this->registerEvent('subscriptions.cancel','Cancel subscription');  
        $this->registerEvent('subscriptions.expire','Subscription expires');  
        // Db tables
        $this->createDbTable('SubscriptionPlansSchema');
        $this->createDbTable('SubscriptionPlanFeaturesSchema');
        $this->createDbTable('SubscriptionsSchema');
        $this->createDbTable('SubscriptionTransactionsSchema');
        $this->createDbTable('PlanFeatureTypesSchema');
        // Options
        $this->createOption('subscriptions.driver','paypal-subscriptions');  
        $this->createOption('subscriptions.ipn.logs',true);  
        $this->createOption('subscriptions.redirects',[
            'success_url' => '',
            'cancel_url'  => '',
            'error_url'   => ''
        ]);           
        // Services
        $this->registerService('Subscriptions');
    }   

    /**
     * UnInstall extension
     *
     * @return void
     */
    public function unInstall()
    {
        $this->unRegisterService('Subscriptions'); 
    }

    /**
     * Post install actions
     *
     * @return void
     */
    public function postInstall()
    {
        // Add subscriptions plans
        Extension::run('Classes\\SubscriptionPlans','subscriptions',function($instance) {
            $instance->create('free','free-plan.json','subscriptions');   
            $instance->create('premium','premium-plan.json','subscriptions');     
            $instance->create('pro','pro-plan.json','subscriptions');          
        });

        $this->runService('subscriptions',function($service) {
            $service->saveFeatureType('api-access','Api Access','Api access token and documentation.',-1);            
        });
    }
}
