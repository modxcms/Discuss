
Dis.panel.UserGroups = function(config) {
    config = config || {};
    Ext.apply(config,{
        title: 'User Groups'
        ,autoHeight: true
        ,forceLayout: true
        ,items: [{
            html: '<p>'+_('discuss.usergroups.intro_msg')+'</p>'
            ,border: false
            ,bodyCssClass: 'panel-desc'
        },{
            title: ''
            ,xtype: 'dis-tree-usergroups'
            ,autoHeight: true
            ,cls: 'main-wrapper'
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
        ,expandFirst: false
        ,tbar: [{
            text: _('discuss.usergroup_add')
            ,handler: function(btn,e) {this.createUserGroup(btn,e,false);}
            ,scope: this
        }]
    })
    Dis.tree.UserGroups.superclass.constructor.call(this,config);
};
Ext.extend(Dis.tree.UserGroups,MODx.tree.UserGroup,{
    getMenu: function() {
        var n = this.cm.activeNode.attributes;
        var m = [];
        switch (n.type) {
            case 'usergroup':
                m.push({
                    text: _('discuss.usergroup_add')
                    ,handler: this.createUserGroup
                });
                m.push({
                    text: _('discuss.usergroup_update')
                    ,handler: this.updateUserGroup
                });
                m.push('-');
                m.push({
                    text: _('discuss.usergroup_remove')
                    ,handler: this.removeUserGroup
                });
                break;
            case 'user':
                m.push({
                    text: _('discuss.usergroup_user_remove')
                    ,handler: this.removeUserFromGroup
                });
                break;

        }
        return m;
    }
    
    ,createUserGroup: function(btn,e,n) {
        var r = {};
        if (n !== false) {
            var id = this.cm.activeNode.id.substr(2).split('_');id = id[1];
            r['parent'] = id;
        } else {r['parent'] = 0;}
        
        if (!this.windows.createUserGroup) {
            this.windows.createUserGroup = MODx.load({
                xtype: 'dis-window-usergroup-create'
                ,record: r
                ,listeners: {
                    'success':{fn:function() {this.refreshNode(this.cm.activeNode.id);},scope:this}
                }
            });
        }
        this.windows.createUserGroup.setValues(r);
        this.windows.createUserGroup.show(e.target);
    }
    
    ,updateUserGroup: function(btn,e) {
        var n = this.cm.activeNode;
        var id = n.id.substr(2).split('_');id = id[1];
        
        location.href = '?a=' + Dis.request.a + '&action=mgr/usergroup/update&id=' + id;
    }
    
    ,removeUserGroup: function(btn,e) {
        var n = this.cm.activeNode;
        var id = n.id.substr(2).split('_');id = id[1];
        
        MODx.msg.confirm({
            title: _('warning')
            ,text: _('discuss.usergroup_remove_confirm')
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

    ,removeUserFromGroup: function(btn,e) {
        var n = this.cm.activeNode.attributes;
        MODx.msg.confirm({
            title: _('warning')
            ,text: _('discuss.usergroup_user_remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/usergroup/user/remove'
                ,user: n.user
                ,usergroup: n.usergroup
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
        title: _('discuss.usergroup_add')
        ,id: this.ident
        ,height: 150
        ,width: 625
        ,url: Dis.config.connector_url
        ,action: 'mgr/usergroup/create'
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
                    ,description: MODx.expandHelp ? '' : _('discuss.usergroup_name_desc')
                    ,name: 'name'
                    ,id: this.ident+'-name'
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-name'
                    ,html: _('discuss.usergroup_name_desc')
                    ,cls: 'desc-under'

                },{
                    xtype: 'textfield'
                    ,fieldLabel:  _('discuss.usergroup_name_color')
                    ,description: MODx.expandHelp ? '' : _('discuss.usergroup_name_color_desc')
                    ,name: 'color'
                    ,id: this.ident+'-color'
                    ,anchor: '100%'
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-color'
                    ,html: _('discuss.usergroup_name_color_desc')
                    ,cls: 'desc-under'

                }]
            },{
                columnWidth: .5
                ,items: [{
                    xtype: 'checkbox'
                    ,boxLabel: _('discuss.usergroup_post_based')
                    ,description: MODx.expandHelp ? '' : _('discuss.usergroup_post_based_desc')
                    ,name: 'post_based'
                    ,id: this.ident+'-post-based'
                    ,inputValue: true
                    ,listeners: {
                        'check': {fn:function() {
                            var tf = Ext.getCmp('dis-'+this.ident+'-min-posts');
                            tf.setDisabled(!tf.disabled);
                        },scope:this}
                    }
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-post-based'
                    ,html: _('discuss.usergroup_post_based_desc')
                    ,cls: 'desc-under'

                },{
                    xtype: 'numberfield'
                    ,fieldLabel: _('discuss.usergroup_min_posts')
                    ,description: _('discuss.usergroup_min_posts_desc')
                    ,name: 'min_posts'
                    ,id: this.ident+'-min-posts'
                    ,width: 100
                },{
                    xtype: MODx.expandHelp ? 'label' : 'hidden'
                    ,forId: this.ident+'-min-posts'
                    ,html: _('discuss.usergroup_min_posts_desc')
                    ,cls: 'desc-under'


                }]
            }]
        }]
    });
    Dis.window.CreateUserGroup.superclass.constructor.call(this,config);
};
Ext.extend(Dis.window.CreateUserGroup,MODx.Window);
Ext.reg('dis-window-usergroup-create',Dis.window.CreateUserGroup);