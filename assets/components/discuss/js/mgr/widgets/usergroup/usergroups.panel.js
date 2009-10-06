
Dis.panel.UserGroups = function(config) {
    config = config || {};
    Ext.apply(config,{
        title: 'User Groups'
        ,autoHeight: true
        ,forceLayout: true
        ,items: [{
            html: '<h2>User Groups</h2>'
            ,border: false
        },{
            html: '<p>Manage User Groups.</p><br />'
            ,border: false
        },{
            title: ''
            ,xtype: 'dis-tree-usergroups'
            ,autoHeight: true
        }]
    });
    Dis.panel.UserGroups.superclass.constructor.call(this,config);
};
Ext.extend(Dis.panel.UserGroups,MODx.Panel);
Ext.reg('dis-panel-usergroups',Dis.panel.UserGroups);





Dis.tree.UserGroups = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-tree-usergroups'
        ,url: Dis.config.connector_url
        ,action: 'mgr/usergroup/getNodes'
        ,enableDD: false
        ,enableDrag: false
        ,enableDrop: false
        ,rootVisible: false
        ,useDefaultToolbar: true
        ,remoteToolbar: false
        ,tbar: [{
            text: 'Create User Group'
            ,handler: function(btn,e) { this.createUserGroup(btn,e,false); }
            ,scope: this
        }]
    })
    Dis.tree.UserGroups.superclass.constructor.call(this,config);
};
Ext.extend(Dis.tree.UserGroups,MODx.tree.UserGroup,{
    createUserGroup: function(btn,e,n) {
        var r = {};
        if (n !== false) {
            var id = this.cm.activeNode.id.substr(2).split('_'); id = id[1];
            r['parent'] = id;
        } else { r['parent'] = 0; }
        
        if (!this.windows.createUserGroup) {
            this.windows.createUserGroup = MODx.load({
                xtype: 'dis-window-usergroup-create'
                ,record: r
                ,listeners: {
                    'success':{fn:function() { this.refreshNode(this.cm.activeNode.id); },scope:this}
                }
            });
        }
        this.windows.createUserGroup.setValues(r);
        this.windows.createUserGroup.show(e.target);
    }
    
    ,updateUserGroup: function(btn,e) {
        var n = this.cm.activeNode;
        var id = n.id.substr(2).split('_'); id = id[1];
        
        location.href = '?a=' + Dis.request.a + '&action=usergroup/update&id=' + id;
    }
    
    ,removeUserGroup: function(btn,e) {
        var n = this.cm.activeNode;
        var id = n.id.substr(2).split('_'); id = id[1];
        
        MODx.msg.confirm({
            title: _('warning')
            ,text: _('user_group_remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/usergroup/remove'
                ,id: id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
});
Ext.reg('dis-tree-usergroups',Dis.tree.UserGroups);


Dis.window.CreateUserGroup = function(config) {
    config = config || {};
    this.ident = config.ident || 'ccat'+Ext.id();
    Ext.applyIf(config,{
        title: 'Create User Group'
        ,id: this.ident
        ,height: 150
        ,width: 475
        ,url: Dis.config.connector_url
        ,action: 'mgr/usergroup/create'
        ,fields: [{
            xtype: 'hidden'
            ,name: 'parent'
            ,id: 'dis-'+this.ident+'-parent'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('name')
            ,name: 'name'
            ,id: 'dis-'+this.ident+'-name'
            ,width: 300
        },{
            xtype: 'checkbox'
            ,fieldLabel: 'Post-Based'
            ,description: 'If true, this User Group will be based on Post counts. Once a User reaches the specified count, they will become a part of this User Group.'
            ,name: 'post_based'
            ,id: 'dis-'+this.ident+'-post-based'
            ,inputValue: true
            ,listeners: {
                'check': {fn:function() {
                    var tf = Ext.getCmp('dis-'+this.ident+'-min-posts');
                    tf.setDisabled(!tf.disabled);
                },scope:this}
            }
        },{
            xtype: 'numberfield'
            ,fieldLabel: 'Minimum Posts'
            ,name: 'min_posts'
            ,id: 'dis-'+this.ident+'-min-posts'
            ,width: 50
            ,disabled: true
        },{
            xtype: 'textfield'
            ,fieldLabel: 'Name Color'
            ,name: 'color'
            ,description: 'The color a User in this User Group will have in the Online section.'
            ,id: 'dis-'+this.ident+'-color'
            ,width: 200
        },{
            xtype: 'textfield'
            ,fieldLabel: 'Image'
            ,name: 'image'
            ,id: 'dis-'+this.ident+'-image'
            ,width: 200
        }]
    });
    Dis.window.CreateUserGroup.superclass.constructor.call(this,config);
};
Ext.extend(Dis.window.CreateUserGroup,MODx.Window);
Ext.reg('dis-window-usergroup-create',Dis.window.CreateUserGroup);