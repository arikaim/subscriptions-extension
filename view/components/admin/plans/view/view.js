/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function SubscriptionPlansView() {
    var self = this;
   
    this.init = function() {
        this.loadMessages('subscriptions::admin.messages');
        paginator.init('plans_rows');   
    };

    this.initRows = function() {
       
        $('.status-dropdown').dropdown({
            onChange: function(value) {               
                var uuid = $(this).attr('uuid');
                subscriptionPlans.setStatus(uuid,value);
            }
        });  

        arikaim.ui.button('.delete-button',function(element) {
            var uuid = $(element).attr('uuid');
            var title = $(element).attr('data-title');
            var message = arikaim.ui.template.render(self.getMessage('remove.content'),{ title: title });
            
            modal.confirmDelete({ 
                title: self.getMessage('remove.title'),
                description: message
            },function() {
                subscriptionPlans.delete(uuid,function(result) {
                    arikaim.ui.table.removeRow('#row_' + uuid); 
                    arikaim.page.toastMessage(result.message);    
                },function(error) {
                    arikaim.page.toastMessage({
                        class: 'error',
                        message: error
                    });    
                });
            });
        });

        arikaim.ui.button('.edit-button',function(element) {
            var uuid = $(element).attr('uuid');    
            arikaim.ui.setActiveTab('#edit_plan','.subscription-plan-item');
            arikaim.page.loadContent({
                id: 'subscription_plan_content',
                component: 'subscriptions::admin.plans.edit',
                params: { uuid: uuid }
            });          
        });
    };
};

var subscriptionPlansView = createObject(SubscriptionPlansView,ControlPanelView);

arikaim.component.onLoaded(function() {
    subscriptionPlansView.init();
    subscriptionPlansView.initRows();  
}); 