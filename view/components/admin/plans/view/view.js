/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function SubscriptionPlansView() {
    var self = this;
   
    this.init = function() {
        this.loadMessages('subscriptions::admin.messages');
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
            arikaim.page.loadContent({
                id: 'plan_details',
                component: 'subscriptions::admin.plans.edit',
                params: { uuid: uuid }
            });          
        });

        arikaim.ui.button('.details-button',function(element) {
            var uuid = $(element).attr('uuid');    
            $('#plan_details').show();

            arikaim.page.loadContent({
                id: 'plan_details',
                component: 'subscriptions::admin.plans.details',
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