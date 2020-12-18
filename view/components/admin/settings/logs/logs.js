'use strict';

$(document).ready(function() {
    $('#save_ipn_data').checkbox({
        onChecked: function() {      
            options.save('subscriptions.ipn.logs',1);   
        },
        onUnchecked: function() {
            options.save('subscriptions.ipn.logs',0);   
        }
    });
});