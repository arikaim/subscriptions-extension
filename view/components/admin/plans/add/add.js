'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.form.onSubmit('#subscriptoion_plan_form',function() {
        return subscriptionPlans.add('#subscriptoion_plan_form');
    },function(result) {
        arikaim.ui.form.showMessage(result.message);
        arikaim.page.loadContent({
            id: 'subscription_plan_content',
            component: 'subscriptions::admin.plans.view'           
        });         
    },function(error) {
    });
});