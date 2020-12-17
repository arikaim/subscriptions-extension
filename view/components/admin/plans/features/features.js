/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function PlanFeaturesView() {
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
        arikaim.ui.button('.add-feature',function(element) {
            var uuid = $(element).attr('plan-uuid');

            arikaim.page.loadContent({
                id: 'feature_content',
                component: 'subscriptions::admin.plans.features.add',
                params: { uuid: uuid }
            });    
        });
    };

    this.initRows = function() {
       
        arikaim.ui.button('.delete-feaure',function(element) {
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

        arikaim.ui.button('.edit-feature',function(element) {
            var uuid = $(element).attr('uuid');    
           
            arikaim.page.loadContent({
                id: 'feature_content',
                component: 'subscriptions::admin.plans.features.edit',
                params: { uuid: uuid }
            });          
        });
    };
};

var planFeaturesView = new PlanFeaturesView();

$(document).ready(function() {  
    planFeaturesView.init();
    planFeaturesView.initRows();  
}); 