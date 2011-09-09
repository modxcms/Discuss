Dis.panel.UserGroup = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-panel-usergroup'
        ,url: Dis.config.connector_url
        ,baseParams: {}
        ,fileUpload: true
        ,items: [{
            html: '<h2>'+_('discuss.usergroup_new')+'</h2>'
            ,border: false
            ,id: 'dis-usergroup-header'
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,border: true
            ,defaults: {
                autoHeight: true, bodyStyle: 'padding: 15px;'
            }
            ,forceLayout: true
            ,items: [{
                title: _('general_information')
                ,layout: 'form'
                ,items: [{                    
                    xtype: 'statictextfield'
                    ,fieldLabel: _('id')
                    ,name: 'id'
                    ,submitValue: true
                },{
                    xtype: 'textfield'
                    ,fieldLabel: _('name')
                    ,name: 'name'
                    ,width: 250
                    ,allowBlank: false
                },{
                    xtype: 'checkbox'
                    ,fieldLabel: _('discuss.usergroup_post_based')
                    ,description: _('discuss.usergroup_post_based_desc')
                    ,name: 'post_based'
                    ,inputValue: true
                },{
                    xtype: 'numberfield'
                    ,fieldLabel: _('discuss.usergroup_min_posts')
                    ,description: _('discuss.usergroup_min_posts_desc')
                    ,name: 'min_posts'
                    ,id: 'dis-usergroup-min-posts'
                    ,width: 50
                },{
                    xtype: 'textfield'
                    ,fieldLabel:  _('discuss.usergroup_name_color')
                    ,description: _('discuss.usergroup_name_color_desc')
                    ,name: 'color'
                    ,width: 200
                },{
                    xtype: 'displayfield'
                    ,fieldLabel: _('discuss.usergroup_image')
                    ,description: _('discuss.usergroup_image_desc')
                    ,name: 'image'
                    ,width: 200
                },{
                    id: 'ug-image-preview'
                    ,html: ''
                    ,border: false
                },{
                    xtype: 'textfield'
                    ,inputType: 'file'
                    ,name: 'image'
                    ,width: 200
                }]
            },{
                title: _('discuss.members')
                ,layout: 'form'
                ,defaults: { autoHeight: true }
                ,forceLayout: true
                ,items: [{
                    html: '<p>'+_('discuss.user_members.intro_msg')+'</p>'
                    ,border: false
                },{
                    xtype: 'dis-grid-usergroup-members'
                    ,width: '97%'
                    ,usergroup: config.usergroup
                    ,preventRender: true
                }]
            }/*,{
                title: _('discuss.boards')
                ,layout: 'form'
                ,defaults: { autoHeight: true }
                ,forceLayout: true
                ,items: [{
                    html: '<p>'+_('discuss.user_boards.intro_msg')+'</p>'
                    ,border: false
                },{
                    xtype: 'dis-grid-usergroup-boards'
                    ,width: '97%'
                    ,usergroup: config.usergroup
                    ,preventRender: true
                }]
            }*/]
        }]
        ,listeners: {
            'setup': {fn:this.setup,scope:this}
            ,'beforeSubmit': {fn:this.beforeSubmit,scope:this}
            ,'success': {fn:this.success,scope:this}
        }
    });
    Dis.panel.UserGroup.superclass.constructor.call(this,config);
};
Ext.extend(Dis.panel.UserGroup,MODx.FormPanel,{
    setup: function() {
        if (!this.config.usergroup) return;
        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/usergroup/get'
                ,id: this.config.usergroup
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.getForm().setValues(r.object);
                    
                    var d = Ext.decode(r.object.members);
                    Ext.getCmp('dis-grid-usergroup-members').getStore().loadData(d);
                    
                    var b = Ext.decode(r.object.boards);
                    Ext.getCmp('dis-grid-usergroup-boards').getStore().loadData(b);
                    
                    Ext.getCmp('dis-usergroup-header').getEl().update('<h2>'+'UserGroup'+': '+r.object.name+'</h2>');

                    if (r.object.badge_full) {
                        Ext.get('ug-image-preview').update('<img src="'+r.object.badge_full+'?pq='+Math.floor(Math.random()*11)+'" alt="" />');
                    }
                },scope:this}
            }
        });
    }
    ,beforeSubmit: function(o) {
        Ext.apply(o.form.baseParams,{
            members: Ext.getCmp('dis-grid-usergroup-members').encode()
            ,boards: Ext.getCmp('dis-grid-usergroup-boards').encode()
        });
    }
    ,success: function(o) {
        if (!this.config['usergroup']) { 
            location.href = '?a='+Dis.request.a+'&action=usergroup/update&user='+o.result.object.id;
        } else {
            Ext.getCmp('dis-btn-save').setDisabled(false);
            Ext.getCmp('dis-grid-usergroup-members').getStore().commitChanges();
            Ext.getCmp('dis-grid-usergroup-boards').getStore().commitChanges();
        }
    }
});
Ext.reg('dis-panel-usergroup',Dis.panel.UserGroup);