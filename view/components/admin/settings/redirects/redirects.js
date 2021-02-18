'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.form.onSubmit('#redirects_form',function() {
        var successUrl = $('#success_url').val().trim();
        var cancelUrl = $('#cancel_url').val().trim();
        var errorlUrl = $('#error_url').val().trim();
        
        return options.save('subscriptions.redirects',{
            success_url: successUrl,
            cancel_url: cancelUrl,
            error_url: errorlUrl
        });   

    },function(result) {
        arikaim.ui.form.showMessage(result.message);       
    },function(error) {
    });
});