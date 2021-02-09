'use strict';

$(document).ready(function() {
    arikaim.ui.form.onSubmit('#plan_feature_form',function() {
        return subscriptionPlans.updateFeature('#plan_feature_form');
    },function(result) {
        arikaim.ui.form.showMessage(result.message); 
        var planId = $('#plan_id').val();
        
        planFeaturesView.loadList(planId);    
    },function(error) {
    });
});