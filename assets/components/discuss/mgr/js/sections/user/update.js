Ext.onReady(function() {
    MODx.load({ xtype: 'dis-page-user-update'});
});

Dis.page.UpdateUser = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        formpanel: 'dis-panel-user'
        ,buttons: [{
            text: _('save')
            ,id: 'dis-btn-save'
            ,process: 'mgr/user/update'
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
            xtype: 'dis-panel-user'
            ,user: Dis.request.user
            ,renderTo: 'dis-panel-user-div'
        }]
    }); 
    Dis.page.UpdateUser.superclass.constructor.call(this,config);
};
Ext.extend(Dis.page.UpdateUser,MODx.Component);
Ext.reg('dis-page-user-update',Dis.page.UpdateUser);