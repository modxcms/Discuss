
Dis.panel.Boards = function(config) {
    config = config || {};
    Ext.apply(config,{
        title: _('discuss.boards')
        ,autoHeight: true
        ,items: [{
            html: '<p>'+_('discuss.boards.intro_msg')+'</p>'
            ,border: false
            ,bodyCssClass: 'panel-desc'
        },{
            xtype: 'dis-tree-boards'
            ,autoHeight: true
            ,width: '100%'
            ,cls: 'main-wrapper'
        }]
    });
    Dis.panel.Boards.superclass.constructor.call(this,config);
};
Ext.extend(Dis.panel.Boards,MODx.Panel);
Ext.reg('dis-panel-boards',Dis.panel.Boards);

Dis.tree.Boards = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-tree-boards'
        ,url: Dis.config.connector_url
        ,action: 'mgr/board/getNodes'
        ,tbar: [{
            text: _('discuss.board_create')
            ,handler: this.createBoard
            ,scope: this
        },'-',{
            text: _('discuss.category_create')
            ,handler: this.createCategory
            ,scope: this
        },'-',{
            text: _('discuss.refresh')
            ,handler: this.refresh
            ,scope: this
        }]
        //,enableDD: true
        ,rootVisible: false
    });
    Dis.tree.Boards.superclass.constructor.call(this,config);
};
Ext.extend(Dis.tree.Boards,MODx.tree.Tree,{
    windows: {}
        
    ,approveMultiple: function() {
        var t = Ext.getCmp('dis-tree-boards');
        var r = t.encode();
        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/board/movefromtree'
                ,data: r
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.setupLoad();
                },scope:this}
            }
        });
    }
    
    ,updateBoard: function(btn,e) {
        var id = this.cm.activeNode ? this.cm.activeNode.attributes.pk : 0;
        location.href = '?a='+MODx.request.a+'&board='+id+'&action=mgr/board/update';
    }
    
    ,createBoard: function(btn,e) {
        var r = {};
        if (this.cm.activeNode) {
            r['parent'] = this.cm.activeNode.attributes.classKey == 'disBoard' ? this.cm.activeNode.attributes.pk : 0;
            r.category = this.cm.activeNode.attributes.category;
        }
        
        if (!this.windows.createBoard) {
            this.windows.createBoard = MODx.load({
                xtype: 'dis-window-board-create'
                ,record: r
                ,listeners: {
                    'success': {fn:function() { this.refresh(); },scope:this}
                }
            });
        }
        this.windows.createBoard.show(e.target);
    }
    
    
    ,removeBoard: function(btn,e) {
        if (!this.cm.activeNode) return false;
        
        MODx.msg.confirm({
            text: _('discuss.board_remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/board/remove'
                ,id: this.cm.activeNode.attributes.pk
            }
            ,listeners: {
                'success': {fn:function(r) { this.refresh(); },scope:this}
            }
        });
        return true;
    }
    
    
    ,createCategory: function(btn,e) {        
        if (!this.windows.createCategory) {
            this.windows.createCategory = MODx.load({
                xtype: 'dis-window-category-create'
                ,listeners: {
                    'success': {fn:function() { this.refresh(); },scope:this}
                }
            });
        }
        this.windows.createCategory.show(e.target);
        return true;
    }
    ,updateCategory: function(btn,e) {
        var r = this.cm.activeNode.attributes;
        r.id = r.pk;
        if (!this.windows.updateCategory) {
            this.windows.updateCategory = MODx.load({
                xtype: 'dis-window-category-update'
                ,record: r
                ,listeners: {
                    'success': {fn:function() { this.refresh(); },scope:this}
                }
            });
        }
        this.windows.updateCategory.reset();
        this.windows.updateCategory.setValues(r);
        this.windows.updateCategory.show(e.target);
        return true;
    }
    
    ,removeCategory: function(btn,e) {
        if (!this.cm.activeNode) return false;
        
        MODx.msg.confirm({
            text: _('discuss.category_remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/category/remove'
                ,id: this.cm.activeNode.attributes.pk
            }
            ,listeners: {
                'success': {fn:function(r) { this.refresh(); },scope:this}
            }
        });
        return true;
    }
    
    ,_handleDrag: function(dropEvent) {
        
        var encNodes = this.encode();
        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                data: encNodes
                ,action: 'mgr/board/sort'
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.reloadNode(dropEvent.target.parentNode);
                },scope:this}
                ,'failure': {fn:function(r) {
                    MODx.form.Handler.errorJSON(r);
                    return false;
                },scope:this}
            }
        });
    }

    ,getMenu: function(n) {
        var a = n.attributes;
        var m = [];
        switch (a.classKey) {
            case 'disBoard':
                m.push({
                    text: _('discuss.board_edit')
                    ,handler: this.updateBoard
                });
                m.push('-');
                m.push({
                    text: _('discuss.board_create_here')
                    ,handler: this.createBoard
                });
                m.push('-');
                m.push({
                    text: _('discuss.board_remove')
                    ,handler: this.removeBoard
                });
                break;
            case 'disCategory':
                m.push({
                    text: _('discuss.category_edit')
                    ,handler: this.updateCategory
                });
                m.push('-');
                m.push({
                    text: _('discuss.board_create_here')
                    ,handler: this.createBoard
                })
                m.push('-');
                m.push({
                    text: _('discuss.category_remove')
                    ,handler: this.removeCategory
                });
                break;
        }
        return m;
    }
});
Ext.reg('dis-tree-boards',Dis.tree.Boards);



Dis.window.CreateBoard = function(config) {
    config = config || {};
    this.ident = config.ident || 'cbrd'+Ext.id();
    Ext.applyIf(config,{
        title: _('discuss.board_create')
        ,id: this.ident
        ,height: 150
        ,width: 625
        ,url: Dis.config.connector_url
        ,action: 'mgr/board/create'
        ,fields: [{
            xtype: 'hidden'
            ,name: 'parent'
            ,id: 'dis-'+this.ident+'-parent'
        },{
            layout: 'column'
            ,border: false
            ,anchor: '100%'
            ,defaults: {
                layout: 'form'
                ,labelAlign: 'top'
                ,anchor: '100%'
                ,border: false
            }
            ,items: [{
                columnWidth: .5
                ,items: [{
                    xtype: 'textfield'
                    ,fieldLabel: _('name')
                    ,name: 'name'
                    ,id: 'dis-'+this.ident+'-name'
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-name'
                    ,html: _('discuss.board_name_desc')
                    ,cls: 'desc-under'
                },{
                    xtype: 'dis-combo-category'
                    ,fieldLabel: _('category')
                    ,name: 'category'
                    ,id: this.ident+'-category'
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-category'
                    ,html: _('discuss.board_category_desc')
                    ,cls: 'desc-under'

                },{
                    xtype: 'textarea'
                    ,fieldLabel: _('description')
                    ,name: 'description'
                    ,id: this.ident+'-description'
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-description'
                    ,html: _('discuss.board_description_desc')
                    ,cls: 'desc-under'
                    
                },{
                    xtype: 'checkbox'
                    ,boxLabel: _('discuss.board_locked')
                    ,description: _('discuss.board_locked_desc')
                    ,hideLabel: true
                    ,name: 'locked'
                    ,id: this.ident+'-locked'
                    ,labelSeparator: ''
                    ,inputValue: 1
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-locked'
                    ,html: _('discuss.board_locked_desc')
                    ,cls: 'desc-under'

                }]
            },{
                columnWidth: .5
                ,items: [{
                    xtype: 'dis-combo-board-status'
                    ,fieldLabel: _('discuss.board_status')
                    ,description: MODx.expandHelp ? '' : _('discuss.board_status_desc')
                    ,name: 'status'
                    ,hiddenName: 'status'
                    ,id: this.ident+'-status'
                    ,anchor: '100%'
                    ,allowBlank: false
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-status'
                    ,html: _('discuss.board_status_desc')
                    ,cls: 'desc-under'

                },{
                    xtype: 'dis-combo-minimum-post-level'
                    ,fieldLabel: _('discuss.minimum_post_level')
                    ,description: MODx.expandHelp ? '' : _('discuss.minimum_post_level_desc')
                    ,name: 'minimum_post_level'
                    ,hiddenName: 'minimum_post_level'
                    ,id: this.ident+'-minimum-post-level'
                    ,anchor: '100%'
                    ,allowBlank: false
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-minimum-post-level'
                    ,html: _('discuss.minimum_post_level_desc')
                    ,cls: 'desc-under'

                },{
                    xtype: 'dis-combo-rtl'
                    ,fieldLabel: _('discuss.lang_direction')
                    ,description: _('discuss.lang_direction_desc')
                    ,name: 'rtl'
                    ,id: this.ident+'-rtl'
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-rtl'
                    ,html: _('discuss.lang_direction_desc')
                    ,cls: 'desc-under'

                },{
                    xtype: 'checkbox'
                    ,boxLabel: _('discuss.board_ignoreable')
                    ,description: MODx.expandHelp ? '' : _('discuss.board_ignoreable_desc')
                    ,name: 'ignoreable'
                    ,id: this.ident+'-ignoreable'
                    ,checked: true
                    ,inputValue: 1
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-ignoreable'
                    ,html: _('discuss.board_ignoreable_desc')
                    ,cls: 'desc-under'
                }]
            }]
        }]
    });
    Dis.window.CreateBoard.superclass.constructor.call(this,config);
};
Ext.extend(Dis.window.CreateBoard,MODx.Window);
Ext.reg('dis-window-board-create',Dis.window.CreateBoard);



Dis.window.CreateCategory = function(config) {
    config = config || {};
    this.ident = config.ident || 'ccat'+Ext.id();
    Ext.applyIf(config,{
        title: _('discuss.category_create')
        ,id: this.ident
        ,height: 150
        ,width: 625
        ,url: Dis.config.connector_url
        ,action: 'mgr/category/create'
        ,fields: [{
            layout: 'column'
            ,border: false
            ,anchor: '100%'
            ,defaults: {
                layout: 'form'
                ,labelAlign: 'top'
                ,anchor: '100%'
                ,border: false
            }
            ,items: [{
                columnWidth: .5
                ,items: [{
                    xtype: 'textfield'
                    ,fieldLabel: _('name')
                    ,description: MODx.expandHelp ? '' : _('discuss.category_name_desc')
                    ,name: 'name'
                    ,id: this.ident+'-name'
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-name'
                    ,html: _('discuss.category_name_desc')
                    ,cls: 'desc-under'

                },{
                    xtype: 'textarea'
                    ,fieldLabel: _('description')
                    ,description: MODx.expandHelp ? '' : _('discuss.category_description_desc')
                    ,name: 'description'
                    ,id: this.ident+'-description'
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-description'
                    ,html: _('discuss.category_description_desc')
                    ,cls: 'desc-under'

                },{
                    xtype: 'checkbox'
                    ,boxLabel: _('discuss.board_collapsible')
                    ,description: MODx.expandHelp ? '' : _('discuss.board_collapsible_desc')
                    ,hideLabel: true
                    ,name: 'collapsible'
                    ,id: this.ident+'-collapsible'
                    ,checked: true
                    ,inputValue: 1
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-collapsible'
                    ,html: _('discuss.board_collapsible_desc')
                    ,cls: 'desc-under'

                }]
            },{
                columnWidth: .5
                ,items: [{
                    xtype: 'textfield'
                    ,fieldLabel: _('discuss.category_default_moderators')
                    ,description: MODx.expandHelp ? '' : _('discuss.category_default_moderators_desc')
                    ,name: 'default_moderators'
                    ,id: this.ident+'-default-board-moderators'
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-default-board-moderators'
                    ,html: _('discuss.category_default_moderators_desc')
                    ,cls: 'desc-under'

                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('discuss.category_default_usergroups')
                    ,description: MODx.expandHelp ? '' : _('discuss.category_default_usergroups_desc')
                    ,name: 'default_usergroups'
                    ,id: this.ident+'-default-board-usergroups'
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-default-board-usergroups'
                    ,html: _('discuss.category_default_usergroups_desc')
                    ,cls: 'desc-under'

                }]
            }]
        }]
    });
    Dis.window.CreateCategory.superclass.constructor.call(this,config);
};
Ext.extend(Dis.window.CreateCategory,MODx.Window);
Ext.reg('dis-window-category-create',Dis.window.CreateCategory);

Dis.window.UpdateCategory = function(config) {
    config = config || {};
    this.ident = config.ident || 'ucat'+Ext.id();
    Ext.applyIf(config,{
        title: _('discuss.category_update')
        ,id: this.ident
        ,height: 150
        ,width: 625
        ,url: Dis.config.connector_url
        ,action: 'mgr/category/update'
        ,fields: [{
            xtype: 'hidden'
            ,name: 'id'
        },{
            layout: 'column'
            ,border: false
            ,anchor: '100%'
            ,defaults: {
                layout: 'form'
                ,labelAlign: 'top'
                ,anchor: '100%'
                ,border: false
            }
            ,items: [{
                columnWidth: .5
                ,items: [{
                    xtype: 'textfield'
                    ,fieldLabel: _('name')
                    ,description: MODx.expandHelp ? '' : _('discuss.category_name_desc')
                    ,name: 'name'
                    ,id: this.ident+'-name'
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-name'
                    ,html: _('discuss.category_name_desc')
                    ,cls: 'desc-under'

                },{
                    xtype: 'textarea'
                    ,fieldLabel: _('description')
                    ,description: MODx.expandHelp ? '' : _('discuss.category_description_desc')
                    ,name: 'description'
                    ,id: this.ident+'-description'
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-description'
                    ,html: _('discuss.category_description_desc')
                    ,cls: 'desc-under'

                },{
                    xtype: 'checkbox'
                    ,boxLabel: _('discuss.board_collapsible')
                    ,description: MODx.expandHelp ? '' : _('discuss.board_collapsible_desc')
                    ,hideLabel: true
                    ,name: 'collapsible'
                    ,id: this.ident+'-collapsible'
                    ,checked: true
                    ,inputValue: 1
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-collapsible'
                    ,html: _('discuss.board_collapsible_desc')
                    ,cls: 'desc-under'

                }]
            },{
                columnWidth: .5
                ,items: [{
                    xtype: 'textfield'
                    ,fieldLabel: _('discuss.category_default_moderators')
                    ,description: MODx.expandHelp ? '' : _('discuss.category_default_moderators_desc')
                    ,name: 'default_moderators'
                    ,id: this.ident+'-default-board-moderators'
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-default-board-moderators'
                    ,html: _('discuss.category_default_moderators_desc')
                    ,cls: 'desc-under'

                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('discuss.category_default_usergroups')
                    ,description: MODx.expandHelp ? '' : _('discuss.category_default_usergroups_desc')
                    ,name: 'default_usergroups'
                    ,id: this.ident+'-default-board-usergroups'
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-default-board-usergroups'
                    ,html: _('discuss.category_default_usergroups_desc')
                    ,cls: 'desc-under'

                }]
            }]
        }]
    });
    Dis.window.UpdateCategory.superclass.constructor.call(this,config);
};
Ext.extend(Dis.window.UpdateCategory,MODx.Window);
Ext.reg('dis-window-category-update',Dis.window.UpdateCategory);