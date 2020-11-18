"use strict";

$(document).ready(function() {     
    safeCall('subscriptionsView',function(obj) {
        obj.initRows();
    },true);   
}); 