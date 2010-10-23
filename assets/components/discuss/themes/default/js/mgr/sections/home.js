Ext.onReady(function() {
    MODx.load({ xtype: 'dis-page-home'});
});

Dis.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'dis-panel-home'
            ,renderTo: 'dis-panel-home-div'
        }]
    }); 
    Dis.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(Dis.page.Home,MODx.Component);
Ext.reg('dis-page-home',Dis.page.Home);