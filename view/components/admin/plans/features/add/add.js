'use strict';

$(document).ready(function() {
    arikaim.ui.form.onSubmit('#plan_feature_form',function() {
        return subscriptionPlans.addFeature('#plan_feature_form');
    },function(result) {
        arikaim.ui.form.showMessage(result.message);
        
        arikaim.page.loadContent({
            id: 'features_items',
            component: 'subscriptions::admin.plans.features.list'           
        });         
    },function(error) {
    });
});