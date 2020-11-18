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
        // Plans
        $this->addApiRoute('POST','/api/subscriptions/admin/plans/add','PlansControlPanel','add','session');   
        $this->addApiRoute('PUT','/api/subscriptions/admin/plans/update','PlansControlPanel','update','session'); 
        $this->addApiRoute('DELETE','/api/subscriptions/admin/plans/delete/{uuid}','PlansControlPanel','delete','session'); 
        $this->addApiRoute('PUT','/api/subscriptions/admin/plans/status','PlansControlPanel','setStatus','session');  
        // Plan Features
        $this->addApiRoute('POST','/api/subscriptions/admin/plan/feature/add','PlanFeaturesControlPanel','add','session');   
        $this->addApiRoute('PUT','/api/subscriptions/admin/plan/feature/update','PlanFeaturesControlPanel','update','session'); 
        $this->addApiRoute('DELETE','/api/subscriptions/admin/plan/feature/delete/{uuid}','PlanFeaturesControlPanel','delete','session'); 
       
        // Pages
        $this->addPageRoute('/subscription/{name}/{slug}/{user_uuid}[/{params}]','SubscriptionPages','create','subscriptions>subscription');  
        $this->addPageRoute('/subscription/success/{name}/[{data}]','SubscriptionPages','success','subscriptions>subscription.success');
        $this->addPageRoute('/subscription/cancel/{name}/[{data}]','SubscriptionPages','cancel','subscriptions>subscription.cancel');   

        // Api               
       // $this->addApiRoute('POST','/api/checkout/notify[/{name}]','CheckoutApi','notify');   

        // Subscriptions
        $this->registerEvent('subscriptions.init','Init subscription pyment');  
        $this->registerEvent('subscriptions.success','Success payment');  
        $this->registerEvent('subscriptions.cancel','Success payment');  

        // Create db tables
        $this->createDbTable('SubscriptionPlansSchema');
        $this->createDbTable('SubscriptionPlanFeaturesSchema');
        $this->createDbTable('SubscriptionsSchema');
       
        // Current subscriptions driver
        $this->createOption('subscriptions.current','paypal');      
    }   
}
