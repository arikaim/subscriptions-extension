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

        search.init({
            id: 'subscriptions_rows',
            component: 'checkout::admin.subscriptions.view.rows',
            event: 'subscriptions.search.load'
        },'subscriptions');  
        
        arikaim.events.on('subscriptions.search.load',function(result) {      
            paginator.reload();
            self.initRows();    
        },'subscriptionsSearch');
    };

    this.initRows = function() {
        arikaim.ui.button('.subscription-details',function(element) {
            var uuid = $(element).attr('uuid');
            return arikaim.page.loadContent({
                id: 'subscriptions_content',
                component: 'checkout::admin.subscriptions.details',
                params: { uuid: uuid }
            });
        });
    };
};

var subscriptionsView = new SubscriptionsView();

$(document).ready(function() {  
    subscriptionsView.init();
}); 