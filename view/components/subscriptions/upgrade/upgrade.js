'use strict';

$(document).ready(function() {

    $('.billing-toggle').checkbox({
        onChecked: function() {    
            changePlanPrice('annual');
        },
        onUnchecked: function() {
            changePlanPrice('monthly');
        }
    });

    function changePlanPrice(type) {
        $('.plan-price').each(function() {
            var monthly = $(this).attr('monthly-price');           
            var annual = $(this).attr('annual-price');
            var price = (type == 'annual') ? annual : monthly;
            $(this).html(price)              
        });
        $('.subscribe-button').each(function() {
            var monthly = $(this).attr('monthly-url');           
            var annual = $(this).attr('annual-url');
            var url = (type == 'annual') ? annual : monthly;
            $(this).attr('href',url);
        });

    }
});