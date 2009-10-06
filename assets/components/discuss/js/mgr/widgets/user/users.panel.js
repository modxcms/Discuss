
Dis.panel.Users = function(config) {
    config = config || {};
    Ext.apply(config,{
        title: 'Users'
        ,autoHeight: true
        ,items: [{
            html: '<h2>Users</h2>'
            ,border: false
        },{
            html: '<p>Manage users.</p><br />'
            ,border: false
        },{
            xtype: 'dis-grid-users'
            ,autoHeight: true
            ,preventRender: true
        }]
    });
    Dis.panel.Users.superclass.constructor.call(this,config);
};
Ext.extend(Dis.panel.Users,MODx.Panel);
Ext.reg('dis-panel-users',Dis.panel.Users);


Dis.grid.Users = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-grid-users'
        ,url: Dis.config.connector_url
        ,baseParams: { action: 'mgr/user/getList' }
        ,save_action: 'mgr/user/updateFromGrid'
        ,fields: ['id','username','menu']
        ,paging: true
        ,autosave: true
        ,remoteSort: true
        ,width: '95%'
        ,columns: [{
            header: 'ID'
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 300
        },{
            header: 'Username'
            ,dataIndex: 'username'
            ,sortable: true
            ,width: 300
            ,editor: { xtype: 'textfield' ,allowBlank: true }
        }]
        ,tbar: [{
            text: 'Create User'
            ,handler: this.createUser
            ,scope: this
        }]
    });
    Dis.grid.Users.superclass.constructor.call(this,config)
};
Ext.extend(Dis.grid.Users,MODx.grid.Grid,{
    removeUser: function() {        
        MODx.msg.confirm({
            title: _('warning')
            ,text: 'Are you sure you want to remove this user?'
            ,url: this.config.url
            ,params: {
                action: 'mgr/user/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.removeActiveRow,scope:this}
            }
        });
    }
    ,createUser: function() {
        location.href = '?a='+MODx.request.a+'&action=user/create';
    }
    ,updateUser: function() {
        var id = this.menu.record.id;
        location.href = '?a='+MODx.request.a+'&user='+id+'&action=user/update';
    }
});
Ext.reg('dis-grid-users',Dis.grid.Users);
