'use strict';

arikaim.component.onLoaded(function() {    
    safeCall('subscriptionsView',function(obj) {
        obj.initRows();
    },true);   
}); 