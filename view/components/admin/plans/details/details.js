'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.button('.close-button',function(element) {
        $('#plan_details').hide(400);
    });
}); 