Dis.panel.Board = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-panel-board'
        ,url: Dis.config.connector_url
        ,baseParams: {}
        ,cls: 'container form-with-labels'
        ,items: [{
            html: '<h2>'+_('discuss.board_new')+'</h2>'
            ,border: false
            ,id: 'dis-board-header'
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,defaults: {
                autoHeight: true
                ,border: true
				,bodyCssClass: 'tab-panel-wrapper'
            }
            ,forceLayout: true
            ,deferredRender: false
            ,stateful: true
            ,stateId: 'dis-board-tabpanel'
            ,stateEvents: ['tabchange']
            ,getState:function() {
                return {activeTab:this.items.indexOf(this.getActiveTab())};
            }
            ,border: false
            ,items: [{
                title: _('general_information')
                ,layout: 'form'
                ,items: [{
                    layout: 'column'
					,cls: 'main-wrapper'
                    ,border: false
                    ,anchor: '100%'
                    ,defaults: {
                        layout: 'form'
                        ,labelAlign: 'top'
                        ,anchor: '100%'
                        ,border: false
                    }
                    ,items: [{
                        columnWidth: .6
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
                            ,description: MODx.expandHelp ? '' : _('discuss.board_name_desc')
                            ,name: 'name'
                            ,id: 'dis-board-name'
                            ,anchor: '100%'
                            ,allowBlank: false
                        },{
                            xtype: MODx.expandHelp ? 'label' : 'hidden'
                            ,forId: 'dis-board-name'
                            ,html: _('discuss.board_name_desc')
                            ,cls: 'desc-under'
                        },{
                            xtype: 'dis-combo-category'
                            ,fieldLabel: _('discuss.category')
                            ,description: MODx.expandHelp ? '' : _('discuss.board_category_desc')
                            ,name: 'category'
                            ,hiddenName: 'category'
                            ,id: 'dis-board-category'
                            ,anchor: '100%'
                            ,allowBlank: false
                        },{
                            xtype: MODx.expandHelp ? 'label' : 'hidden'
                            ,forId: 'dis-board-category'
                            ,html: _('discuss.board_category_desc')
                            ,cls: 'desc-under'
                        },{
                            xtype: 'textarea'
                            ,fieldLabel: _('description')
                            ,description: MODx.expandHelp ? '' : _('discuss.board_description_desc')
                            ,name: 'description'
                            ,id: 'dis-board-description'
                            ,anchor: '100%'
                        },{
                            xtype: MODx.expandHelp ? 'label' : 'hidden'
                            ,forId: 'dis-board-description'
                            ,html: _('discuss.board_description_desc')
                            ,cls: 'desc-under'
                        }]
                    },{
                        columnWidth: .4
                        ,items: [{
                            xtype: 'dis-combo-board-status'
                            ,fieldLabel: _('discuss.board_status')
                            ,description: MODx.expandHelp ? '' : _('discuss.board_status_desc')
                            ,name: 'status'
                            ,hiddenName: 'status'
                            ,id: 'dis-board-status'
                            ,anchor: '100%'
                            ,allowBlank: false
                        },{
                            xtype: MODx.expandHelp ? 'label' : 'hidden'
                            ,forId: 'dis-board-status'
                            ,html: _('discuss.board_status_desc')
                            ,cls: 'desc-under'
                        },{
                            xtype: 'dis-combo-minimum-post-level'
                            ,fieldLabel: _('discuss.minimum_post_level')
                            ,description: MODx.expandHelp ? '' : _('discuss.minimum_post_level_desc')
                            ,name: 'minimum_post_level'
                            ,hiddenName: 'minimum_post_level'
                            ,id: 'dis-board-minimum-post-level'
                            ,anchor: '100%'
                            ,allowBlank: false
                        },{
                            xtype: MODx.expandHelp ? 'label' : 'hidden'
                            ,forId: 'dis-board-minimum-post-level'
                            ,html: _('discuss.minimum_post_level_desc')
                            ,cls: 'desc-under'
                        },{
                            xtype: 'dis-combo-rtl'
                            ,fieldLabel: _('discuss.lang_direction')
                            ,description: MODx.expandHelp ? '' : _('discuss.lang_direction_desc')
                            ,name: 'rtl'
                            ,id: 'dis-board-rtl'
                            ,anchor: '100%'
                        },{
                            xtype: MODx.expandHelp ? 'label' : 'hidden'
                            ,forId: 'dis-board-rtl'
                            ,html: _('discuss.lang_direction_desc')
                            ,cls: 'desc-under'
                        },{
                            xtype: 'checkbox'
                            ,boxLabel: _('discuss.board_locked')
                            ,description: _('discuss.board_locked_desc')
                            ,hideLabel: true
                            ,name: 'locked'
                            ,id: 'dis-board-locked'
                            ,labelSeparator: ''
                            ,inputValue: 1
                        },{
                            xtype: MODx.expandHelp ? 'label' : 'hidden'
                            ,forId: 'dis-board-locked'
                            ,html: _('discuss.board_locked_desc')
                            ,cls: 'desc-under'
                        },{
                            xtype: 'checkbox'
                            ,boxLabel: _('discuss.board_ignoreable')
                            ,description: MODx.expandHelp ? '' : _('discuss.board_ignoreable_desc')
                            ,hideLabel: true
                            ,name: 'ignoreable'
                            ,id: 'dis-board-ignoreable'
                            ,labelSeparator: ''
                            ,inputValue: 1
                        },{
                            xtype: MODx.expandHelp ? 'label' : 'hidden'
                            ,forId: 'dis-board-ignoreable'
                            ,html: _('discuss.board_ignoreable_desc')
                            ,cls: 'desc-under'
                        }]
                    }]
                }]
            },{
                title: _('discuss.moderators')
                ,defaults: { autoHeight: true }
                ,items: [{
                    html: '<p>'+_('discuss.board_moderators_msg')+'</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'dis-grid-board-moderators'
					,cls: 'main-wrapper'
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
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'dis-grid-board-usergroups'
					,cls: 'main-wrapper'
                    ,preventRender: true
                    ,board: config.board
                    ,width: '97%'
                }]
            }]
        }]
        ,listeners: {
            'setup': {fn:this.setup,scope:this}
            ,'beforeSubmit': {fn:this.beforeSubmit,scope:this}
            ,'success': {fn:this.success,scope:this},
            render : function(panel) {
                Ext.getCmp('dis-board-category').getStore().load();
            }
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
                    
                    Ext.getCmp('dis-board-header').getEl().update('<h2>'+'Board'+': '+r.object.name+'</h2>');
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