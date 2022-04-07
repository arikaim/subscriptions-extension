'use strict';

function SubscriptionsApi() {

    this.cancel = function(onSuccess, onError) {
        return arikaim.delete('/api/subscription/cancel', onSuccess, onError);          
    };
    
}

var subscriptionsApi = new SubscriptionsApi();
