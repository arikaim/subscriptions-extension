/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function SubscriptionsControlPanel() {
   
    this.init = function() {    
        arikaim.ui.tab('.tab-item','tab_content');
    };   
}

var subscriptions = new SubscriptionsControlPanel();

$(document).ready(function() {
    subscriptions.init();
});