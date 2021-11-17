'use strict';

arikaim.component.onLoaded(function() {
    $('.subscription-plans-dropdown').dropdown({});
    arikaim.ui.form.addRules("#add_subscription_form");

    arikaim.ui.form.onSubmit('#add_subscription_form',function() {
        return subscriptionsControlPanel.add('#add_subscription_form');
    },function(result) {
        arikaim.ui.form.showMessage(result.message);
        arikaim.page.loadContent({
            id: 'subscriptions_content',
            component: 'subscriptions::admin.subscriptions.view'           
        });         
    },function(error) {
    });
});