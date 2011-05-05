Dis.panel.User = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-panel-user'
        ,url: Dis.config.connector_url
        ,baseParams: {}
        ,items: [{
            html: '<h2>'+_('discuss.user_new')+'</h2>'
            ,border: false
            ,id: 'dis-user-header'
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,border: true
            ,defaults: {
                autoHeight: true, bodyStyle: 'padding: 10px;'
            }
            ,items: [{
                title: _('general_information')
                ,layout: 'form'
                ,items: [{                    
                    xtype: 'statictextfield'
                    ,fieldLabel: _('id')
                    ,name: 'id'
                    ,submitValue: true
                },{
                    xtype: 'statictextfield'
                    ,fieldLabel: _('discuss.username')
                    ,name: 'username'
                    ,width: 250
                    ,allowBlank: false
                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('discuss.user_email')
                    ,name: 'email'
                    ,width: 250
                    ,allowBlank: false
                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('discuss.user_name_first')
                    ,name: 'name_first'
                    ,width: 250
                    ,allowBlank: true
                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('discuss.user_name_last')
                    ,name: 'name_last'
                    ,width: 250
                    ,allowBlank: true
                },{
                    xtype: 'datefield'
                    ,fieldLabel: _('discuss.user_birthdate')
                    ,name: 'birthdate'
                    ,width: 250
                    ,allowBlank: true
                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('discuss.user_website')
                    ,name: 'website'
                    ,width: 250
                    ,allowBlank: true
                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('discuss.user_location')
                    ,name: 'location'
                    ,width: 250
                    ,allowBlank: true
                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('discuss.custom_title')
                    ,name: 'title'
                    ,width: 250
                    ,allowBlank: true
                },{
                    xtype: 'textarea'
                    ,fieldLabel: _('discuss.user_signature')
                    ,name: 'signature'
                    ,width: 500
                    ,grow: true
                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('discuss.posts')
                    ,name: 'posts'
                    ,width: 250
                    ,allowBlank: true
                },{
                    xtype: 'modx-combo-usergroup'
                    ,fieldLabel: _('discuss.primary_group')
                    ,description: _('discuss.primary_group_desc')
                    ,name: 'primary_group'
                    ,hiddenName: 'primary_group'
                    ,width: 250
                    ,allowBlank: true

                },{
                    xtype: 'checkbox'
                    ,fieldLabel: _('discuss.user_show_email')
                    ,name: 'show_email'
                    ,labelSeparator: ''
                    ,inputValue: 1
                },{
                    xtype: 'checkbox'
                    ,fieldLabel: _('discuss.user_show_online')
                    ,name: 'show_online'
                    ,labelSeparator: ''
                    ,inputValue: 1
                },{ html: "<hr />" ,border: false },{
                    xtype: 'statictextfield'
                    ,fieldLabel: _('discuss.registered_on')
                    ,name: 'createdon'
                    ,width: 250
                    ,allowBlank: true
                },{
                    xtype: 'checkbox'
                    ,fieldLabel: _('discuss.synced')
                    ,name: 'synced'
                    ,labelSeparator: ''
                    ,inputValue: 1
                    ,disabled: true
                },{
                    xtype: 'statictextfield'
                    ,fieldLabel: _('discuss.synced_at')
                    ,name: 'syncedat'
                    ,width: 250
                    ,allowBlank: true
                },{
                    xtype: 'statictextfield'
                    ,fieldLabel: _('discuss.source')
                    ,name: 'source'
                    ,width: 250
                    ,allowBlank: true
                },{
                    xtype: 'statictextfield'
                    ,fieldLabel: _('discuss.last_login')
                    ,name: 'last_login'
                    ,width: 250
                    ,allowBlank: true
                },{
                    xtype: 'statictextfield'
                    ,fieldLabel: _('discuss.last_active')
                    ,name: 'last_active'
                    ,width: 250
                    ,allowBlank: true
                },{
                    xtype: 'statictextfield'
                    ,fieldLabel: _('discuss.ip')
                    ,name: 'ip'
                    ,width: 250
                    ,allowBlank: true
                }]
            }/*,{
                title: 'Activity'
                ,layout: 'form'
                ,defaults: { autoHeight: true }
                ,items: [{
                    html: '<p>Activity info, including track IP and other usage, goes here.</p>'
                    ,border: false
                }]
            }*/,{
                title: _('discuss.posts')
                ,layout: 'form'
                ,defaults: { autoHeight: true }
                ,items: [{
                    html: '<p>'+_('discuss.user_posts.intro_msg')+'</p>'
                    ,border: false
                },{
                    xtype: 'dis-grid-user-posts'
                    ,width: '97%'
                    ,user: config.user
                    ,preventRender: true
                }]
            }/*,{
                title: 'Permissions'
                ,layout: 'form'
                ,defaults: { autoHeight: true }
                ,items: [{
                    html: '<p>'+_('discuss.user_perms.intro_msg')+'</p>'
                    ,border: false
                }]
            }*/]
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
            //perms: Ext.getCmp('dis-user-permissions').encode()
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