Dis.panel.User = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-panel-user'
        ,url: Dis.config.connector_url
        ,baseParams: {}
        ,items: [{
            html: '<h2>'+'New User'+'</h2>'
            ,border: false
            ,id: 'dis-user-header'
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,border: true
            ,defaults: {
                autoHeight: true, bodyStyle: 'padding: 1em;'
            }
            ,items: [{
                title: _('general_information')
                ,layout: 'form'
                ,items: [{                    
                    xtype: 'statictextfield'
                    ,fieldLabel: _('id')
                    ,name: 'user'
                    ,submitValue: true
                },{
                    xtype: 'textfield'
                    ,fieldLabel: 'Username'
                    ,name: 'username'
                    ,width: 250
                    ,allowBlank: false
                },{
                    xtype: 'textfield'
                    ,fieldLabel: 'Email'
                    ,name: 'email'
                    ,width: 250
                    ,allowBlank: false
                },{
                    xtype: 'textfield'
                    ,fieldLabel: 'First Name'
                    ,name: 'name_first'
                    ,width: 250
                    ,allowBlank: false
                },{
                    xtype: 'textfield'
                    ,fieldLabel: 'Last Name'
                    ,name: 'name_last'
                    ,width: 250
                    ,allowBlank: false
                },{
                    xtype: 'datefield'
                    ,fieldLabel: 'Birthdate'
                    ,name: 'birthdate'
                    ,width: 250
                    ,allowBlank: false
                },{
                    xtype: 'textfield'
                    ,fieldLabel: 'Website'
                    ,name: 'website'
                    ,width: 250
                    ,allowBlank: false
                },{
                    xtype: 'textfield'
                    ,fieldLabel: 'Location'
                    ,name: 'location'
                    ,width: 250
                    ,allowBlank: false
                },{
                    xtype: 'textarea'
                    ,fieldLabel: 'Signature'
                    ,name: 'signature'
                    ,width: 500
                    ,grow: true
                },{
                    xtype: 'checkbox'
                    ,boxLabel: 'Show Email'
                    ,name: 'show_email'
                    ,labelSeparator: ''
                    ,inputValue: 1
                },{
                    xtype: 'checkbox'
                    ,boxLabel: 'Show Online'
                    ,name: 'show_online'
                    ,labelSeparator: ''
                    ,inputValue: 1
                }]
            },{
                title: 'Activity'
                ,layout: 'form'
                ,defaults: { autoHeight: true }
                ,items: [{
                    html: '<p>Activity info, including track IP and other usage, goes here.</p>'
                    ,border: false
                }]
            },{
                title: 'Posts'
                ,layout: 'form'
                ,defaults: { autoHeight: true }
                ,items: [{
                    html: '<p>These are all the posts made by this user.</p>'
                    ,border: false
                },{
                    xtype: 'dis-grid-user-posts'
                    ,width: '97%'
                    ,user: config.user
                    ,preventRender: true
                }]
            },{
                title: 'Permissions'
                ,layout: 'form'
                ,defaults: { autoHeight: true }
                ,items: [{
                    html: '<p>Here you can set permissions for this user.</p>'
                    ,border: false
                }]
            }]
        }]
        ,listeners: {
            'setup': {fn:this.setup,scope:this}
            ,'beforeSubmit': {fn:this.beforeSubmit,scope:this}
            ,'success': {fn:this.success,scope:this}
        }
    });
    Dis.panel.User.superclass.constructor.call(this,config);
};
Ext.extend(Dis.panel.User,MODx.FormPanel,{
    setup: function() {
        if (!this.config.user) return;
        Ext.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/user/get'
                ,id: this.config.user
            }
            ,scope: this
            ,success: function(r) {
                r = Ext.decode(r.responseText);
                if (r.success) {
                    this.getForm().setValues(r.object);
                    Ext.getCmp('dis-user-header').getEl().update('<h2>'+'User'+': '+r.object.username+'</h2>');
                } else MODx.form.Handler.errorJSON(r);
            }
        });
    }
    ,beforeSubmit: function(o) {
        Ext.apply(o.form.baseParams,{
            //tags: Ext.getCmp('rm-grid-package-tag').encode()            
        });
    }
    ,success: function(o) {
        if (!this.config['user']) { 
            location.href = '?a='+Dis.request.a+'&action=user/update&user='+o.result.object.id;
        } else {
            Ext.getCmp('dis-btn-save').setDisabled(false);
        }
    }
});
Ext.reg('dis-panel-user',Dis.panel.User);