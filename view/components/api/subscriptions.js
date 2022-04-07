'use strict';

function SubscriptionsApi() {

    this.cancel = function(onSuccess, onError) {
        return arikaim.delete('/api/subscriptions/cancel/', onSuccess, onError);          
    };
    
}

var subscriptionsApi = new SubscriptionsApi();
