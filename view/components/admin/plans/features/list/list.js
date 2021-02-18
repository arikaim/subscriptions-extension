'use strict';

arikaim.component.onLoaded(function() {   
    safeCall('planFeaturesView',function(obj) {
        obj.initRows();
    },true);   
}); 