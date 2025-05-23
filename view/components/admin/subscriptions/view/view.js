/**
 *  Arikaim
 *  @copyright  Copyright (c)  <info@arikaim.com>
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

        $('.status-dropdown').dropdown({
            onChange: function(value) {
                var uuid = $(this).attr('uuid');
                subscriptionsControlPanel.setStatus(uuid,value);               
            }
        });

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

arikaim.component.onLoaded(function() {
    subscriptionsView.init();
    subscriptionsView.initRows();
}); 