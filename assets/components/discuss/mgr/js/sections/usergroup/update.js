Dis.page.UpdateUserGroup = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        formpanel: 'dis-panel-usergroup'
        ,buttons: [{
            text: _('save')
            ,id: 'dis-btn-save'
            ,process: 'mgr/usergroup/update'
            ,method: 'remote'
            ,keys: [{
                key: 's'
                ,alt: true
                ,ctrl: true
            }]
        },'-',{
            text: _('cancel')
            ,id: 'dis-btn-back'
            ,handler: function() {
                location.href = '?a='+Dis.request.a;
            }
            ,scope: this
        }]
        ,components: [{
            xtype: 'dis-panel-usergroup'
            ,usergroup: Dis.request.id
            ,record: config.record || {}
            ,renderTo: 'dis-panel-usergroup-div'
        }]
    });
    Dis.page.UpdateUserGroup.superclass.constructor.call(this,config);
};
Ext.extend(Dis.page.UpdateUserGroup,MODx.Component);
Ext.reg('dis-page-usergroup-update',Dis.page.UpdateUserGroup);