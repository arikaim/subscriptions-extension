'use strict';

$(document).ready(function() {
    $('#drivers_dropdown').dropdown({
        onChange: function(value) {                    
            options.save('subscriptions.driver',value);
        }
    });
});