'use strict';

arikaim.component.onLoaded(function() {
    arikaim.ui.tab('.settings-tab-item','settings_content');

    arikaim.events.on('driver.config',function(element,name,category) {
        arikaim.ui.setActiveTab('#driver_tab');

        return drivers.loadConfig(name,'settings_content');           
    },'driverConfig'); 
});