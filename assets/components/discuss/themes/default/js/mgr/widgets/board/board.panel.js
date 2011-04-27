Dis.panel.Board = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-panel-board'
        ,url: Dis.config.connector_url
        ,baseParams: {}
        ,items: [{
            html: '<h2>'+_('discuss.board_new')+'</h2>'
            ,border: false
            ,id: 'dis-board-name'
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,deferredRender: false
            ,defaults: { autoHeight: true ,bodyStyle: 'padding: 11px;' }
            ,border: true
            ,labelWidth: 150
            ,items: [{
                title: _('general_information')
                ,layout: 'form'
                ,items: [{                    
                    xtype: 'hidden'
                    ,name: 'id'
                },{
                    xtype: 'hidden'
                    ,name: 'parent'
                    ,value: Dis.request.parent ? Dis.request.parent : 0
                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('name')
                    ,name: 'name'
                    ,width: 300
                    ,allowBlank: false
                },{
                    xtype: 'dis-combo-category'
                    ,fieldLabel: _('discuss.category')
                    ,name: 'category'
                    ,hiddenName: 'category'
                    ,width: 300
                    ,allowBlank: false
                },{
                    xtype: 'dis-combo-minimum-post-level'
                    ,fieldLabel: _('discuss.minimum_post_level')
                    ,description: _('discuss.minimum_post_level_desc')
                    ,name: 'minimum_post_level'
                    ,hiddenName: 'minimum_post_level'
                    ,width: 300
                    ,allowBlank: false
                },{
                    xtype: 'textarea'
                    ,fieldLabel: _('description')
                    ,name: 'description'
                    ,width: 500
                },{
                    xtype: 'checkbox'
                    ,fieldLabel: _('discuss.board_ignoreable')
                    ,description: _('discuss.board_ignoreable_desc')
                    ,name: 'ignoreable'
                    ,labelSeparator: ''
                    ,inputValue: 1
                }]
            },{
                title: _('discuss.moderators')
                ,defaults: { autoHeight: true }
                ,items: [{
                    html: '<p>'+_('discuss.board_moderators_msg')+'</p>'
                    ,border: false
                },{
                    xtype: 'dis-grid-board-moderators'
                    ,preventRender: true
                    ,board: config.board
                    ,width: '97%'
                }]
            },{
                title: _('discuss.usergroup_access')
                ,defaults: { autoHeight: true }
                ,items: [{
                    html: '<p>'+_('discuss.board_usergroups_msg')+'</p>'
                    ,border: false
                },{
                    xtype: 'dis-grid-board-usergroups'
                    ,preventRender: true
                    ,board: config.board
                    ,width: '97%'
                }]
            }]
        }]
        ,listeners: {
            'setup': {fn:this.setup,scope:this}
            ,'beforeSubmit': {fn:this.beforeSubmit,scope:this}
            ,'success': {fn:this.success,scope:this}
        }
    });
    Dis.panel.Board.superclass.constructor.call(this,config);
};
Ext.extend(Dis.panel.Board,MODx.FormPanel,{
    setup: function() {
        if (!this.config.board) return;
        Ext.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/board/get'
                ,id: this.config.board
            }
            ,scope: this
            ,success: function(r) {
                r = Ext.decode(r.responseText);
                if (r.success) {
                    this.getForm().setValues(r.object);

                    var d = Ext.decode(r.object.moderators);
                    Ext.getCmp('dis-grid-board-moderators').getStore().loadData(d);
                    
                    var u = Ext.decode(r.object.usergroups);
                    Ext.getCmp('dis-grid-board-usergroups').getStore().loadData(u);
                    
                    Ext.getCmp('dis-board-name').getEl().update('<h2>'+'Board'+': '+r.object.name+'</h2>');
                } else MODx.form.Handler.errorJSON(r);
            }
        });
    }
    ,beforeSubmit: function(o) {
        Ext.apply(o.form.baseParams,{
            moderators: Ext.getCmp('dis-grid-board-moderators').encode()
            ,usergroups: Ext.getCmp('dis-grid-board-usergroups').encode()
        });
    }
    ,success: function(o) {
        if (Ext.isEmpty(this.config['board'])) {
            location.href = '?a='+Dis.request.a+'&action=board/update&board='+o.result.object.id;
        } else {
            Ext.getCmp('dis-btn-save').setDisabled(false);
            Ext.getCmp('dis-grid-board-moderators').getStore().commitChanges();
            Ext.getCmp('dis-grid-board-usergroups').getStore().commitChanges();
        }
    }
});
Ext.reg('dis-panel-board',Dis.panel.Board);