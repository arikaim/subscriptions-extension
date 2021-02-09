'use strict';

$(document).ready(function() {  
    $('.subscription-plans-dropdown').dropdown({
        onChange: function(value) {             
            arikaim.page.loadContent({
                id: 'plan_edit_form_content',
                component: 'subscriptions::admin.plans.edit.tabs',
                params: { uuid: value }
            });    
        }
    });
});