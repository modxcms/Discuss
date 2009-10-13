Dis.panel.UserGroup = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-panel-usergroup'
        ,url: Dis.config.connector_url
        ,baseParams: {}
        ,items: [{
            html: '<h2>'+'New UserGroup'+'</h2>'
            ,border: false
            ,id: 'dis-usergroup-header'
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,border: true
            ,defaults: {
                autoHeight: true, bodyStyle: 'padding: 1em;'
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
                    ,fieldLabel: 'Post-Based'
                    ,description: 'If true, this User Group will be based on Post counts. Once a User reaches the specified count, they will become a part of this User Group.'
                    ,name: 'post_based'
                    ,inputValue: true
                },{
                    xtype: 'numberfield'
                    ,fieldLabel: 'Minimum Posts'
                    ,name: 'min_posts'
                    ,id: 'dis-usergroup-min-posts'
                    ,width: 50
                },{
                    xtype: 'textfield'
                    ,fieldLabel: 'Name Color'
                    ,name: 'color'
                    ,description: 'The color a User in this User Group will have in the Online section.'
                    ,width: 200
                },{
                    xtype: 'textfield'
                    ,fieldLabel: 'Image'
                    ,name: 'image'
                    ,width: 200
                }]
            },{
                title: 'Members'
                ,layout: 'form'
                ,defaults: { autoHeight: true }
                ,items: [{
                    html: '<p>'+'View all the members of this Group.'+'</p>'
                    ,border: false
                },{
                    xtype: 'dis-grid-usergroup-members'
                    ,width: '97%'
                    ,usergroup: config.usergroup
                    ,preventRender: true
                }]
            },{
                title: 'Boards'
                ,layout: 'form'
                ,defaults: { autoHeight: true }
                ,items: [{
                    html: '<p>'+'Here you can manage the Boards this User Group can view.'+'</p>'
                    ,border: false
                },{
                    xtype: 'dis-grid-usergroup-boards'
                    ,width: '97%'
                    ,usergroup: config.usergroup
                    ,preventRender: true
                }]
            }]
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