
Dis.panel.Boards = function(config) {
    config = config || {};
    Ext.apply(config,{
        title: _('discuss.boards')
        ,autoHeight: true
        ,items: [{
            html: '<p>'+_('discuss.boards.intro_msg')+'</p><br />'
            ,border: false
        },{
            xtype: 'dis-tree-boards'
            ,autoHeight: true
            ,width: '97%'
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
    })
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
        location.href = '?a='+MODx.request.a+'&board='+id+'&action=board/update';
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
        ,width: 475
        ,url: Dis.config.connector_url
        ,action: 'mgr/board/create'
        ,fields: [{
            xtype: 'textfield'
            ,fieldLabel: _('name')
            ,name: 'name'
            ,id: 'dis-'+this.ident+'-name'
            ,width: 300
        },{
            xtype: 'hidden'
            ,name: 'parent'
            ,id: 'dis-'+this.ident+'-parent'
        },{
            xtype: 'dis-combo-category'
            ,fieldLabel: _('category')
            ,name: 'category'
            ,id: 'dis-'+this.ident+'-category'
        },{
            xtype: 'textarea'
            ,fieldLabel: _('description')
            ,name: 'description'
            ,id: 'dis-'+this.ident+'-description'
            ,width: 300
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('discuss.board_ignoreable')
            ,name: 'ignoreable'
            ,description: _('discuss.board_ignoreable_desc')
            ,id: 'dis-'+this.ident+'-ignoreable'
            ,checked: true
            ,inputValue: 1
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
        title: 'Create Category'
        ,id: this.ident
        ,height: 150
        ,width: 475
        ,url: Dis.config.connector_url
        ,action: 'mgr/category/create'
        ,fields: [{
            xtype: 'textfield'
            ,fieldLabel: _('name')
            ,name: 'name'
            ,id: 'dis-'+this.ident+'-name'
            ,width: 300
        },{
            xtype: 'textarea'
            ,fieldLabel: _('description')
            ,name: 'description'
            ,id: 'dis-'+this.ident+'-description'
            ,width: 300
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('discuss.board_collapsible')
            ,name: 'collapsible'
            ,description: _('discuss.board_collapsible_desc')
            ,id: 'dis-'+this.ident+'-collapsible'
            ,checked: true
            ,inputValue: 1
        }]
    });
    Dis.window.CreateCategory.superclass.constructor.call(this,config);
};
Ext.extend(Dis.window.CreateCategory,MODx.Window);
Ext.reg('dis-window-category-create',Dis.window.CreateCategory);