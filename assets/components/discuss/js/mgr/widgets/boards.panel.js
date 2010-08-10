
Dis.panel.Boards = function(config) {
    config = config || {};
    Ext.apply(config,{
        title: 'Boards'
        ,autoHeight: true
        ,items: [{
            html: '<h2>Boards</h2>'
            ,border: false
        },{
            html: '<p>Manage your boards.</p><br />'
            ,border: false
        },{
            xtype: 'dis-tree-boards'
            ,autoHeight: true
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
            text: 'Create Board'
            ,handler: this.createBoard
            ,scope: this
        },'-',{
            text: 'Create Category'
            ,handler: this.createCategory
            ,scope: this
        },'-',{
            text: 'Refresh'
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
            text: 'Are you sure you want to remove this board and all its subboards entirely?'
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
    }
    
    ,removeCategory: function(btn,e) {
        if (!this.cm.activeNode) return false;
        
        MODx.msg.confirm({
            text: 'Are you sure you want to remove this category and all its boards entirely?'
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
});
Ext.reg('dis-tree-boards',Dis.tree.Boards);



Dis.window.CreateBoard = function(config) {
    config = config || {};
    this.ident = config.ident || 'cbrd'+Ext.id();
    Ext.applyIf(config,{
        title: 'Create Board'
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
            xtype: 'textfield'
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
            ,fieldLabel: 'Ignoreable'
            ,name: 'ignoreable'
            ,description: 'If true, users can select to hide this board from their views.'
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
            ,fieldLabel: 'Collapsible'
            ,name: 'collapsible'
            ,description: 'If true, users can collapse the board to hide its contents.'
            ,id: 'dis-'+this.ident+'-collapsible'
            ,checked: true
            ,inputValue: 1
        }]
    });
    Dis.window.CreateCategory.superclass.constructor.call(this,config);
};
Ext.extend(Dis.window.CreateCategory,MODx.Window);
Ext.reg('dis-window-category-create',Dis.window.CreateCategory);