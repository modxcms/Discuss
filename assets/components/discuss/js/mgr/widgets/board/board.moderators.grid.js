
Dis.grid.BoardModerators = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-grid-board-moderators'
        ,url: Dis.config.connector_url
        ,baseParams: {
            action: 'mgr/board/moderator/getlist'
            ,user: config.board
        }
        ,action: 'mgr/board/moderator/getlist'
        ,fields: ['user','username']
        ,autoHeight: true
        ,primaryKey: 'user'
        ,columns: [{
            header: _('discuss.user')
            ,dataIndex: 'username'
            ,width: 600
        }]
        ,tbar: [{
            text: _('discuss.moderator_add')
            ,handler: this.addModerator
            ,scope: this
        }]
    });
    Dis.grid.BoardModerators.superclass.constructor.call(this,config);
    this.propRecord = Ext.data.Record.create([{name: 'user'},{name:'username'}]);
};
Ext.extend(Dis.grid.BoardModerators,MODx.grid.LocalGrid,{
    getMenu: function() {
        return [{
            text: _('discuss.moderator_remove')
            ,handler: this.remove.createDelegate(this,[{
                title: _('discuss.moderator_remove')
                ,text: _('discuss.moderator_remove_confirm')
            }])
            ,scope: this
        }];
    }
    
    ,addModerator: function(btn,e) {
        this.loadWindow(btn,e,{
           xtype: 'dis-window-board-moderator-create'
           ,listeners: {
                'success': {fn:function(vs) {
                    var rec = new this.propRecord(vs);
                    this.getStore().add(rec);
                },scope:this}
           }
        });
        Ext.getCmp('dis-window-board-moderator-create').fp.getForm().reset();
    }
});
Ext.reg('dis-grid-board-moderators',Dis.grid.BoardModerators);


Dis.window.CreateBoardModerator = function(config) {
    config = config || {};
    this.ident = config.ident || 'cbmod'+Ext.id();
    Ext.applyIf(config,{
        title: _('discuss.moderator_add')
        ,frame: true
        ,id: 'dis-window-board-moderator-create'
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
        }]
    });
    Dis.window.CreateBoardModerator.superclass.constructor.call(this,config);
};
Ext.extend(Dis.window.CreateBoardModerator,MODx.Window,{
    submit: function() {
        var f = this.fp.getForm();
        var fld = f.findField('user');
        
        if (id != '' && this.fp.getForm().isValid()) {
            if (this.fireEvent('success',{
                user: fld.getValue()
                ,username: fld.getRawValue()
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
Ext.reg('dis-window-board-moderator-create',Dis.window.CreateBoardModerator);