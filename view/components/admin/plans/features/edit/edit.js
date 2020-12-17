'use strict';

$(document).ready(function() {
    arikaim.ui.form.onSubmit('#plan_feature_form',function() {
        return subscriptionPlans.updateFeature('#plan_feature_form');
    },function(result) {
        arikaim.ui.form.showMessage(result.message);     
    },function(error) {
    });
});