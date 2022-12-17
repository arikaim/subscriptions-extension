/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function FeatureTypesView() {
    var self = this;
  
    this.init = function() {
        this.loadMessages('subscriptions::admin.messages');
        arikaim.ui.loadComponentButton('.add-feature-type');
    };

    this.loadList = function() {
        arikaim.page.loadContent({
            id: 'feature_type_items',
            component: 'subscriptions::admin.plans.features.type.list'                  
        });    
    };

    this.initRows = function() {
       
        arikaim.ui.button('.delete-feature-type',function(element) {
            var uuid = $(element).attr('uuid');
            var title = $(element).attr('data-title');
            var message = arikaim.ui.template.render(self.getMessage('remove_feature.content'),{ title: title });
            
            modal.confirmDelete({ 
                title: self.getMessage('remove.title'),
                description: message
            },function() {
                subscriptionPlans.deleteFeatureType(uuid,function(result) {
                    arikaim.ui.table.removeRow('#row_' + result.uuid); 
                    $('#feature_content').html('');
                    arikaim.page.toastMessage(result.message);    
                });
            });
        });

        arikaim.ui.loadComponentButton('.edit-feature-type');
    };
};

var featureTypesView = createObject(FeatureTypesView,ControlPanelView);

arikaim.component.onLoaded(function() {  
    featureTypesView.init();
    featureTypesView.initRows();  
}); 