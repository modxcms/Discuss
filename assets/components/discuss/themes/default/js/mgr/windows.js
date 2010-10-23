

Dis.window.UpdatePost = function(config) {
    config = config || {};
    this.ident = config.ident || 'updpost'+Ext.id();
    Ext.applyIf(config,{
        title: _('discuss.post_modify')
        ,id: this.ident
        ,height: 150
        ,width: 565
        ,url: Dis.config.connector_url
        ,action: 'mgr/post/update'
        ,fields: [{
            xtype: 'statictextfield'
            ,fieldLabel: _('id')
            ,name: 'id'
            ,id: 'dis-'+this.ident+'-id'
            ,submitValue: true
            ,width: 300
        },{
            xtype: 'textfield'
            ,fieldLabel: _('discuss.post_title')
            ,name: 'title'
            ,id: 'dis-'+this.ident+'-title'
            ,width: 300
        },{
            xtype: 'modx-combo-user'
            ,fieldLabel: _('discuss.post_author')
            ,name: 'author'
            ,id: 'dis-'+this.ident+'-author'
            ,width: 300
        },{
            xtype: 'textarea'
            ,fieldLabel: _('discuss.post_message')
            ,name: 'message'
            ,id: 'dis-'+this.ident+'-message'
            ,width: 400
            ,grow: true
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('discuss.post_sticky')
            ,description: _('discuss.post_sticky_desc')
            ,name: 'sticky'
            ,id: 'dis-'+this.ident+'-sticky'
            ,checked: false
            ,inputValue: 1
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('discuss.post_locked')
            ,description: _('discuss.post_locked_desc')
            ,name: 'locked'
            ,id: 'dis-'+this.ident+'-locked'
            ,checked: false
            ,inputValue: 1
        },{
            xtype: 'checkbox'
            ,fieldLabel: _('discuss.post_allow_replies')
            ,description: _('discuss.post_allow_replies_desc')
            ,name: 'allow_replies'
            ,id: 'dis-'+this.ident+'-allow-replies'
            ,checked: true
            ,inputValue: 1
        }]
    });
    Dis.window.UpdatePost.superclass.constructor.call(this,config);
};
Ext.extend(Dis.window.UpdatePost,MODx.Window);
Ext.reg('dis-window-post-update',Dis.window.UpdatePost);