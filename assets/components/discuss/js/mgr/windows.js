

Dis.window.UpdatePost = function(config) {
    config = config || {};
    this.ident = config.ident || 'updpost'+Ext.id();
    Ext.applyIf(config,{
        title: 'Update Post'
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
            ,fieldLabel: 'Title'
            ,name: 'title'
            ,id: 'dis-'+this.ident+'-title'
            ,width: 300
        },{
            xtype: 'modx-combo-user'
            ,fieldLabel: 'Author'
            ,name: 'author'
            ,id: 'dis-'+this.ident+'-author'
            ,width: 300
        },{
            xtype: 'textarea'
            ,fieldLabel: 'Message'
            ,name: 'message'
            ,id: 'dis-'+this.ident+'-message'
            ,width: 400
            ,grow: true
        },{
            xtype: 'checkbox'
            ,fieldLabel: 'Sticky'
            ,name: 'sticky'
            ,description: 'If true, the post will appear at the beginning of a board.'
            ,id: 'dis-'+this.ident+'-sticky'
            ,checked: false
            ,inputValue: 1
        },{
            xtype: 'checkbox'
            ,fieldLabel: 'Locked'
            ,name: 'locked'
            ,description: 'If true, this post cannot be replied to or edited.'
            ,id: 'dis-'+this.ident+'-locked'
            ,checked: false
            ,inputValue: 1
        },{
            xtype: 'checkbox'
            ,fieldLabel: 'Allow Replies'
            ,name: 'allow_replies'
            ,description: 'If false, no replies can be posted to this post.'
            ,id: 'dis-'+this.ident+'-allow-replies'
            ,checked: true
            ,inputValue: 1
        }]
    });
    Dis.window.UpdatePost.superclass.constructor.call(this,config);
};
Ext.extend(Dis.window.UpdatePost,MODx.Window);
Ext.reg('dis-window-post-update',Dis.window.UpdatePost);