
Dis.grid.UserGroupMembers = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-grid-usergroup-members'
        ,url: Dis.config.connector_url
        ,baseParams: {
            action: 'mgr/usergroup/member/getlist'
            ,usergroup: config.usergroup
        }
        ,action: 'mgr/usergroup/member/getlist'
        ,fields: ['id','username','role','role_name']
        ,autoHeight: true
        ,paging: true
        ,columns: [{
            header: _('discuss.username')
            ,dataIndex: 'username'
            ,width: 250
        },{
            header: _('discuss.role')
            ,dataIndex: 'role_name'
            ,width: 250
        }]
        ,tbar: [{
            text: _('discuss.member_add')
            ,handler: this.addMember
            ,scope: this
        }]
    });
    Dis.grid.UserGroupMembers.superclass.constructor.call(this,config);
    this.propRecord = Ext.data.Record.create(['id','username','role','role_name']);
};
Ext.extend(Dis.grid.UserGroupMembers,MODx.grid.LocalGrid,{
    getMenu: function() {
        var m = [{
            text: _('discuss.member_remove')
            ,handler: this.remove
        }];
        return m;
    }
    
    ,addMember: function(btn,e) {
        var r = {};        
        if (!this.windows.addMember) {
            this.windows.addMember = MODx.load({
                xtype: 'dis-window-usergroup-member-create'
                ,record: r
                ,listeners: {
                    'success': {fn:function(vs) {
                        var rec = new this.propRecord(vs);
                        this.getStore().add(rec);
                    },scope:this}
                }
            });
        }
        this.windows.addMember.setValues(r);
        this.windows.addMember.show(e.target);
    }
});
Ext.reg('dis-grid-usergroup-members',Dis.grid.UserGroupMembers);



Dis.window.AddUserGroupMember = function(config) {
    config = config || {};
    this.ident = config.ident || 'disaugm'+Ext.id();
    Ext.applyIf(config,{
        title: _('discuss.member_add')
        ,frame: true
        ,id: 'dis-window-usergroup-member-create'
        ,fields: [{
            xtype: 'modx-combo-user'
            ,fieldLabel: _('discuss.user')
            ,name: 'user'
            ,hiddenName: 'user'
            ,id: 'dis-'+this.ident+'-user'
            ,allowBlank: false
            ,editable: true
            ,typeAhead: true
            ,pageSize: 20
        },{
            xtype: 'modx-combo-role'
            ,fieldLabel: _('discuss.role')
            ,name: 'role'
            ,hiddenName: 'role'
            ,id: 'dis-'+this.ident+'-role'
            ,allowBlank: false
        }]
    });
    Dis.window.AddUserGroupMember.superclass.constructor.call(this,config);
};
Ext.extend(Dis.window.AddUserGroupMember,MODx.Window,{
    submit: function() {
        var f = this.fp.getForm();
        var fld = f.findField('user');
        
        if (id != '' && this.fp.getForm().isValid()) {
            if (this.fireEvent('success',{
                id: fld.getValue()
                ,username: fld.getRawValue()
                ,role: f.findField('role').getValue()
                ,role_name: f.findField('role').getRawValue()
            })) {
                this.fp.getForm().reset();
                this.hide();
                return true;
            }
        } else {
            MODx.msg.alert(_('error'),_('discuss.user_err_ns'));
        }
        return true;
    }
});
Ext.reg('dis-window-usergroup-member-create',Dis.window.AddUserGroupMember);