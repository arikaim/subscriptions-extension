'use strict';

$(document).ready(function() {
    $('.currency-dropdown').dropdown({
        onChange: function(value) {
            
        }
    });
    
    arikaim.ui.form.addRules("#subscriptoion_plan_form");
});