/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function SubscriptionsProviderView() {
    var self = this;
   
    this.create = function(uuid, billingType, onSuccess, onError) {
        var data = {
            uuid: uuid,          
            billing_type: billingType
        };

        return arikaim.post('/api/subscriptions/admin/plan/create',data,onSuccess,onError);      
    };

    this.bind = function(uuid, planId, billingType, onSuccess, onError) {
        var data = {
            uuid: uuid,
            plan_id: planId,
            billing_type: billingType
        };

        return arikaim.put('/api/subscriptions/admin/plan/bind',data,onSuccess,onError);      
    };

    this.activate = function(planId, onSuccess, onError) {
        var data = {
            plan_id: planId
        };

        return arikaim.put('/api/subscriptions/admin/plan/activate',data,onSuccess,onError);      
    };
    
    this.init = function() {       
    };

    this.initRows = function() {
       
        arikaim.ui.button('.select-plan',function(element) {
            var uuid = $(element).attr('uuid');   
            var billingType = $(element).attr('billing-type');    
            var contentId = $(element).attr('content-id');  

            arikaim.page.loadContent({
                id: contentId,
                component: 'subscriptions::admin.provider.plans-dropdown',
                params: { class: 'mini basic button fluid' }
            },function(result) {
                $('.provider-plans-dropdown').dropdown({
                    onChange: function(value) {                       
                        subscriptionProviderView.bind(uuid,value,billingType,function(result) {                          
                            $('#' + contentId).html(result.plan_id);
                        });
                    }
                });
            });          
        });

        arikaim.ui.button('.create-plan',function(element) {
            var uuid = $(element).attr('uuid');    
            var contentId = $(element).attr('content-id');
            var billingType = $(element).attr('billing-type');   

            arikaim.page.loadContent({
                id: 'plan_content',
                component: 'subscriptions::admin.provider.create',
                params: { 
                    uuid: uuid,
                    billing_type: billingType
                }
            });   

        });

        arikaim.ui.button('.plan-details',function(element) {
            var uuid = $(element).attr('uuid');    
            var billingType = $(element).attr('billing-type'); 
            var planId = $('#' + billingType + '_plan_' + uuid).html().trim();

            self.loadPlanDetails(planId,billingType);           
        });
    };

    this.loadPlanDetails = function(planId, billingType) {
        arikaim.page.loadContent({
            id: 'plan_content',
            component: 'subscriptions::admin.provider.details',
            params: {                    
                plan_id: planId,
                billing_type: billingType
            }
        });   
    };
};

var subscriptionProviderView = new SubscriptionsProviderView();

$(document).ready(function() {  
    subscriptionProviderView.init();
    subscriptionProviderView.initRows();  
}); 