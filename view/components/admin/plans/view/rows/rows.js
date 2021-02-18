'use strict';

arikaim.component.onLoaded(function() {    
    safeCall('subscriptionPlansView',function(obj) {
        obj.initRows();
    },true);   
}); 