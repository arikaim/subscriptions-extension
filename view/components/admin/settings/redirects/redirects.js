'use strict';

$(document).ready(function() {
    arikaim.ui.form.onSubmit('#redirects_form',function() {
        var successUrl = $('#success_url').val().trim();
        var cancelUrl = $('#cancel_url').val().trim();
        
        return options.save('subscriptions.redirects',{
            success_url: successUrl,
            cancel_url: cancelUrl
        });   

    },function(result) {
        arikaim.ui.form.showMessage(result.message);       
    },function(error) {
    });
});