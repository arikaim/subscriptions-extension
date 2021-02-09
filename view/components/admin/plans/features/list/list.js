'use strict';

$(document).ready(function() {     
    safeCall('planFeaturesView',function(obj) {
        obj.initRows();
    },true);   
}); 