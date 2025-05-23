'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.button('.activate-plan',function(element) {
        var planId = $(element).attr('plan-id');    
      
        return subscriptionProviderView.activate(planId,function(result) {        
            $('#status_content').html(result.status);      
            $('.activate-plan').hide();    
        });       
    });
});