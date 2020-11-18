'use strict';

$(document).ready(function() {     
    safeCall('subscriptionPlansView',function(obj) {
        obj.initRows();
    },true);   
}); 