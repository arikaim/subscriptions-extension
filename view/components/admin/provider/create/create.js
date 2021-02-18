'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.button('.provider-create-plan',function(element) {
        var uuid = $(element).attr('uuid');    
        var billingType = $(element).attr('billing-type');    

        return subscriptionProviderView.create(uuid,billingType,function(result) {                   
            subscriptionProviderView.loadPlanDetails(result.plan_id,billingType); 

            subscriptionProviderView.bind(uuid,result.plan_id,billingType,function(result) {                                       
                $('#' + billingType + '_plan_' + uuid).html(result.plan_id);
            });           
        },function(error) {
        });       
    });
});