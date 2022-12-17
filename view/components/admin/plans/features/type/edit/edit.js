'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.form.onSubmit('#feature_type_form',function() {
        return subscriptionPlans.updateFeatureType('#feature_type_form');
    },function(result) {
        arikaim.ui.form.showMessage(result.message);  
        featureTypesView.loadList();    
    });
});