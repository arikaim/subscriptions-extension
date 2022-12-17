/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function SubscriptionPlans() {
    var self = this;

    this.updateFeatureType = function(formId, onSuccess, onError) {
        return arikaim.put('/api/subscriptions/admin/feature/type/update',formId, onSuccess, onError);          
    };

    this.deleteFeatureType = function(uuid, onSuccess, onError) {
        return arikaim.delete('/api/subscriptions/admin/feature/type/delete/' + uuid, onSuccess, onError);          
    };

    this.addFeatureType = function(formId, onSuccess, onError) {
        return arikaim.post('/api/subscriptions/admin/feature/type/add',formId, onSuccess, onError);          
    };

    this.addFeature = function(formId, onSuccess, onError) {
        return arikaim.post('/api/subscriptions/admin/plan/feature/add',formId, onSuccess, onError);          
    };

    this.add = function(formId, onSuccess, onError) {
        return arikaim.post('/api/subscriptions/admin/plans/add',formId, onSuccess, onError);          
    };

    this.updateFeature = function(formId, onSuccess, onError) {
        return arikaim.put('/api/subscriptions/admin/plan/feature/update',formId, onSuccess, onError);          
    };

    this.update = function(formId, onSuccess, onError) {
        return arikaim.put('/api/subscriptions/admin/plans/update',formId, onSuccess, onError);          
    };
    
    this.deleteFeature = function(uuid, onSuccess, onError) {
        return arikaim.delete('/api/subscriptions/admin/plan/feature/delete/' + uuid, onSuccess, onError);          
    };

    this.delete = function(uuid, onSuccess, onError) {
        return arikaim.delete('/api/subscriptions/admin/plans/delete/' + uuid, onSuccess, onError);          
    };

    this.setStatus = function(uuid, status, onSuccess, onError) {          
        var data = { 
            uuid: uuid,
            status: status 
        };

        return arikaim.put('/api/subscriptions/admin/plans/status',data,onSuccess,onError);      
    };

    this.initEditForm = function() {
        arikaim.ui.form.onSubmit('#subscriptoion_plan_form',function() {
            return self.update('#subscriptoion_plan_form');
        },function(result) {
            arikaim.ui.form.showMessage(result.message);       
        });
    }
}

var subscriptionPlans = new SubscriptionPlans();

arikaim.component.onLoaded(function() {
    arikaim.ui.loadComponentButton('.plan-action-buttons');
});