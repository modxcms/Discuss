Dis.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,items: [{
            html: '<h2>'+_('discuss')+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,bodyStyle: 'padding: 15px'
            ,defaults: { border: false ,autoHeight: true }
            ,border: true
            ,stateful: true
            ,stateId: 'dis-home-tabpanel'
            ,stateEvents: ['tabchange']
            ,getState:function() {
                return {activeTab:this.items.indexOf(this.getActiveTab())};
            }
            ,items: [{
                xtype: 'dis-panel-boards'
            },{
                xtype: 'dis-panel-users'
            },{
                xtype: 'dis-panel-usergroups'
                ,forceLayout: true
            }]
        }]
    });
    Dis.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(Dis.panel.Home,MODx.Panel);
Ext.reg('dis-panel-home',Dis.panel.Home);
