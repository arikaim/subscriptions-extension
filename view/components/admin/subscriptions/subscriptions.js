/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function SubscriptionsControlPanel() {

    this.add = function(formId, onSuccess, onError) {
        return arikaim.post('/api/admin/subscriptions/add',formId,onSuccess,onError);          
    };

    this.setStatus = function(uuid, status, onSuccess, onError) {          
        var data = { 
            uuid: uuid,
            status: status 
        };

        return arikaim.put('/api/admin/subscriptions/status',data,onSuccess,onError);      
    };

    this.getDetails = function(uuid, driverName, onSuccess, onError) { 
        var data = { 
            driver_name: driverName,
            uuid: uuid 
        };
        
        return arikaim.post('/api/subscriptions/admin/subscription/details',data,onSuccess,onError);           
    };
}

var subscriptionsControlPanel = new SubscriptionsControlPanel();

arikaim.component.onLoaded(function() {
    arikaim.ui.tab('.subscription-tab-item','subscriptions_content');
});