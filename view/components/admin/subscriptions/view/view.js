/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
"use strict";

function SubscriptionsView() {
    var self = this;

    this.init = function() {
        arikaim.ui.tab('.transaction-tab-item','subscriptions_content');
        paginator.init('subscriptions_rows');         
    };

    this.initRows = function() {
        arikaim.ui.button('.subscription-details',function(element) {
            var uuid = $(element).attr('uuid');
            return arikaim.page.loadContent({
                id: 'subscriptions_content',
                component: 'subscriptions::admin.subscriptions.details',
                params: { uuid: uuid }
            });
        });
    };
};

var subscriptionsView = new SubscriptionsView();

$(document).ready(function() {  
    subscriptionsView.init();
}); 