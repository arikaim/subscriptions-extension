/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function SubscriptionPlansView() {
    var self = this;
    this.messges = {};

    this.loadMessages = function() {
        if (isObject(this.messages) == true) {
            return;
        }
        arikaim.component.loadProperties('subscriptions::admin.messages',function(params) { 
            self.messages = params.messages;
        }); 
    };

    this.init = function() {
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
            var message = arikaim.ui.template.render(self.messages.remove.content,{ title: title });
            
            modal.confirmDelete({ 
                title: self.messages.remove.title,
                description: message
            },function() {
                subscriptionPlans.delete(uuid,function(result) {
                    arikaim.ui.table.removeRow('#' + uuid); 
                    arikaim.page.toastMessage(result.message);    
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

var subscriptionPlansView = new SubscriptionPlansView();

$(document).ready(function() {  
    subscriptionPlansView.init();
    subscriptionPlansView.initRows();  
}); 