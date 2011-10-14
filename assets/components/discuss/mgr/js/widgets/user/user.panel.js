Dis.panel.User = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-panel-user'
        ,url: Dis.config.connector_url
        ,cls: 'container form-with-labels'
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
                autoHeight: true
            }
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
                            xtype: 'statictextfield'
                            ,fieldLabel: _('id')
                            ,name: 'id'
                            ,anchor: '100%'
                            ,submitValue: true
                        },{
                            xtype: 'statictextfield'
                            ,fieldLabel: _('discuss.username')
                            ,name: 'username'
                            ,anchor: '100%'
                            ,allowBlank: false
                        },{
                            xtype: 'textfield'
                            ,fieldLabel: _('discuss.user_email')
                            ,name: 'email'
                            ,anchor: '100%'
                            ,allowBlank: false
                        },{
                            xtype: 'textfield'
                            ,fieldLabel: _('discuss.user_name_first')
                            ,name: 'name_first'
                            ,anchor: '100%'
                            ,allowBlank: true
                        },{
                            xtype: 'textfield'
                            ,fieldLabel: _('discuss.user_name_last')
                            ,name: 'name_last'
                            ,anchor: '100%'
                            ,allowBlank: true
                        },{
                            xtype: 'textfield'
                            ,fieldLabel: _('discuss.user_website')
                            ,name: 'website'
                            ,anchor: '100%'
                            ,allowBlank: true
                        },{
                            xtype: 'textfield'
                            ,fieldLabel: _('discuss.user_location')
                            ,name: 'location'
                            ,anchor: '100%'
                            ,allowBlank: true
                        },{
                            xtype: 'textfield'
                            ,fieldLabel: _('discuss.custom_title')
                            ,name: 'title'
                            ,anchor: '100%'
                            ,allowBlank: true
                        },{
                            xtype: 'textarea'
                            ,fieldLabel: _('discuss.user_signature')
                            ,name: 'signature'
                            ,anchor: '100%'
                            ,grow: true
                        }]
                    },{
                        columnWidth: .4
                        ,items: [{
                            xtype: 'textfield'
                            ,fieldLabel: _('discuss.posts')
                            ,name: 'posts'
                            ,anchor: '100%'
                            ,allowBlank: true
                        },{
                            xtype: 'modx-combo-usergroup'
                            ,fieldLabel: _('discuss.primary_group')
                            ,description: _('discuss.primary_group_desc')
                            ,name: 'primary_group'
                            ,hiddenName: 'primary_group'
                            ,anchor: '100%'
                            ,allowBlank: true

                        },{
                            xtype: 'datefield'
                            ,fieldLabel: _('discuss.user_birthdate')
                            ,name: 'birthdate'
                            ,anchor: '100%'
                            ,allowBlank: true
                        },{
                            xtype: 'checkbox'
                            ,boxLabel: _('discuss.user_show_email')
                            ,hideLabel: true
                            ,name: 'show_email'
                            ,labelSeparator: ''
                            ,inputValue: 1
                        },{
                            xtype: 'checkbox'
                            ,boxLabel: _('discuss.user_show_online')
                            ,hideLabel: true
                            ,name: 'show_online'
                            ,labelSeparator: ''
                            ,inputValue: 1
                        }]
                    }]
                }]
            },{
                title: 'Activity'
                ,layout: 'form'
                ,items: [{
                    html: '<p>'+_('discuss.user_activity_msg')+'</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
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
                            xtype: 'textfield'
                            ,fieldLabel: _('discuss.registered_on')
                            ,name: 'createdon'
                            ,readOnly: true
                            ,anchor: '100%'
                            ,allowBlank: true
                        },{
                            xtype: 'checkbox'
                            ,boxLabel: _('discuss.synced')
                            ,hideLabel: true
                            ,readOnly: true
                            ,name: 'synced'
                            ,labelSeparator: ''
                            ,inputValue: 1
                            ,disabled: true
                        },{
                            xtype: 'textfield'
                            ,fieldLabel: _('discuss.synced_at')
                            ,name: 'syncedat'
                            ,readOnly: true
                            ,anchor: '100%'
                            ,allowBlank: true
                        },{
                            xtype: 'textfield'
                            ,fieldLabel: _('discuss.source')
                            ,name: 'source'
                            ,readOnly: true
                            ,anchor: '100%'
                            ,allowBlank: true
                        }]
                    },{
                        columnWidth: .4
                        ,items: [{
                            xtype: 'textfield'
                            ,fieldLabel: _('discuss.last_login')
                            ,name: 'last_login'
                            ,readOnly: true
                            ,anchor: '100%'
                            ,allowBlank: true
                        },{
                            xtype: 'textfield'
                            ,fieldLabel: _('discuss.last_active')
                            ,name: 'last_active'
                            ,readOnly: true
                            ,anchor: '100%'
                            ,allowBlank: true
                        },{
                            xtype: 'textfield'
                            ,fieldLabel: _('discuss.ip')
                            ,readOnly: true
                            ,name: 'ip'
                            ,anchor: '100%'
                            ,allowBlank: true
                        }]
                    }]
                }]
            },{
                title: _('discuss.posts')
                ,layout: 'form'
                ,defaults: { autoHeight: true }
                ,items: [{
                    html: '<p>'+_('discuss.user_posts.intro_msg')+'</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'dis-grid-user-posts'
                    ,cls: 'main-wrapper'
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