
Dis.grid.BoardUserGroups = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-grid-board-usergroups'
        ,url: Dis.config.connector_url
        ,baseParams: {
            action: 'mgr/board/group/getlist'
            ,user: config.board
        }
        ,action: 'mgr/board/group/getlist'
        ,fields: ['id','name']
        ,autoHeight: true
        ,columns: [{
            header: _('discuss.usergroup')
            ,dataIndex: 'name'
            ,width: 600
        }]
        ,tbar: [{
            text: _('discuss.usergroup_add')
            ,handler: this.addUserGroup
            ,scope: this
        }]
    });
    Dis.grid.BoardUserGroups.superclass.constructor.call(this,config);
    this.propRecord = Ext.data.Record.create([{name: 'id'},{name:'name'}]);
};
Ext.extend(Dis.grid.BoardUserGroups,MODx.grid.LocalGrid,{
    getMenu: function() {
        return [{
            text: _('discuss.board_usergroup_remove')
            ,handler: this.remove.createDelegate(this,[{
                title: _('discuss.board_usergroup_remove_title')
                ,text: _('discuss.board_usergroup_remove_confirm')
            }])
            ,scope: this
        }];
    }
    
    ,addUserGroup: function(btn,e) {
        this.loadWindow(btn,e,{
           xtype: 'dis-window-board-usergroup-create'
           ,listeners: {
                'success': {fn:function(vs) {
                    var rec = new this.propRecord(vs);
                    this.getStore().add(rec);
                },scope:this}
           }
        });
        Ext.getCmp('dis-window-board-usergroup-create').fp.getForm().reset();
    }
});
Ext.reg('dis-grid-board-usergroups',Dis.grid.BoardUserGroups);


Dis.window.CreateBoardAccess = function(config) {
    config = config || {};
    this.ident = config.ident || 'cbacc'+Ext.id();
    Ext.applyIf(config,{
        title: _('discuss.board_usergroup_add')
        ,frame: true
        ,id: 'dis-window-board-usergroup-create'
        ,fields: [{
            xtype: 'modx-combo-usergroup'
            ,fieldLabel: _('discuss.usergroup')
            ,name: 'usergroup'
            ,hiddenName: 'usergroup'
            ,id: 'dis-'+this.ident+'-usergroup'
            ,allowBlank: false
            ,pageSize: 20
        }]
    });
    Dis.window.CreateBoardAccess.superclass.constructor.call(this,config);
};
Ext.extend(Dis.window.CreateBoardAccess,MODx.Window,{
    submit: function() {
        var f = this.fp.getForm();
        var fld = f.findField('usergroup');
        
        if (id != '' && this.fp.getForm().isValid()) {
            if (this.fireEvent('success',{
                id: fld.getValue()
                ,name: fld.getRawValue()
            })) {
                this.fp.getForm().reset();
                this.hide();
                return true;
            }
        } else {
            MODx.msg.alert(_('error'),_('discuss.usergroup_err_ns'));
        }
        return true;
    }
});
Ext.reg('dis-window-board-usergroup-create',Dis.window.CreateBoardAccess);