/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function SubscriptionsView() {
    var self = this;

    this.init = function() {
        arikaim.ui.tab('.transaction-tab-item','subscriptions_content');
        paginator.init('subscriptions_rows');       
        
        $('.users-dropdown').on('change',function() {
            var selected = $(this).dropdown('get value');
            self.loadRows(selected);    
        });
    };

    this.loadRows = function(userId) {
        var params = (isEmpty(userId) == false) ? { user_id: userId } : null;

        return arikaim.page.loadContent({
            id: 'subscriptions_rows',
            component: 'subscriptions::admin.subscriptions.view.rows',
            params: params
        },function(result) {
            self.initRows();
        });
    };

    this.initRows = function() {
        arikaim.ui.button('.subscription-details',function(element) {
            var uuid = $(element).attr('uuid');
            arikaim.ui.setActiveTab('#details_tab','.subscription-tab-item');
            
            return arikaim.page.loadContent({
                id: 'subscriptions_content',
                component: 'subscriptions::admin.subscriptions.details',
                params: { uuid: uuid }
            });
        });
    };
};

var subscriptionsView = new SubscriptionsView();

arikaim.component.onLoaded(function() {
    subscriptionsView.init();
    subscriptionsView.initRows();
}); 