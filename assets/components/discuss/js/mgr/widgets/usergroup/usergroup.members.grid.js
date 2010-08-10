
Dis.grid.UserGroupMembers = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-grid-usergroup-members'
        ,url: Dis.config.connector_url
        ,baseParams: {
            action: 'mgr/usergroup/member/getlist'
            ,user: config.user
        }
        ,action: 'mgr/usergroup/member/getlist'
        ,fields: ['id','username','role']
        ,autoHeight: true
        ,paging: true
        ,columns: [{
            header: 'Username'
            ,dataIndex: 'username'
            ,width: 250
        }]
        ,tbar: [{
            text: 'Add Member'
            ,handler: this.addMember
            ,scope: this
        }]
    });
    Dis.grid.UserGroupMembers.superclass.constructor.call(this,config);
    this.propRecord = Ext.data.Record.create([{name: 'id'},{name:'username'},{name:'role'}]);
};
Ext.extend(Dis.grid.UserGroupMembers,MODx.grid.LocalGrid,{
    getMenu: function() {
        return [{
            text: 'Remove Member'
            ,handler: this.remove.createDelegate(this,[{
                title: 'Remove Member?'
                ,text: 'Are you sure you want to remove this User from this User Group?'
            }])
            ,scope: this
        }];
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
    
    ,removeMember: function(btn,e) {
        if (!this.menu.record) return false;
        
        MODx.msg.confirm({
            text: 'Are you sure you want to remove this post and all its replies entirely?'
            ,url: this.config.url
            ,params: {
                action: 'mgr/post/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) { this.refresh(); },scope:this}
            }
        });
        return true;
    }
});
Ext.reg('dis-grid-usergroup-members',Dis.grid.UserGroupMembers);



Dis.window.AddUserGroupMember = function(config) {
    config = config || {};
    this.ident = config.ident || 'disaugm'+Ext.id();
    Ext.applyIf(config,{
        title: 'Add User Group Member'
        ,frame: true
        ,id: 'dis-window-usergroup-member-create'
        ,fields: [{
            xtype: 'modx-combo-user'
            ,fieldLabel: 'User'
            ,name: 'user'
            ,hiddenName: 'user'
            ,id: 'dis-'+this.ident+'-user'
            ,allowBlank: false
            ,pageSize: 20
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
                ,role: 0
            })) {
                this.fp.getForm().reset();
                this.hide();
                return true;
            }
        } else {
            MODx.msg.alert(_('error'),'Please select a user.');
        }
        return true;
    }
});
Ext.reg('dis-window-usergroup-member-create',Dis.window.AddUserGroupMember);