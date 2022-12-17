'use strict';

arikaim.component.onLoaded(function() {   
    safeCall('featureTypesView',function(obj) {
        obj.initRows();
    },true);   
}); 