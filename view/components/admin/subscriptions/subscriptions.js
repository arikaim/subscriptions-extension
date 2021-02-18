/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function SubscriptionsControlPanel() {

    this.init = function() {
        arikaim.ui.tab('.subscription-tab-item','subscriptions_content');
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
    subscriptionsControlPanel.init();
});