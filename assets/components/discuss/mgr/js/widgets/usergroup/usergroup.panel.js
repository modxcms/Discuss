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
            ,labelWidth: 150
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
                    ,anchor: '97%'
                    ,allowBlank: false
                },{
                    xtype: 'checkbox'
                    ,fieldLabel: _('discuss.usergroup_post_based')
                    ,description: _('discuss.usergroup_post_based_desc')
                    ,name: 'post_based'
                    ,anchor: '97%'
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
                    ,width: 300
                },{
                    xtype: 'displayfield'
                    ,fieldLabel: _('discuss.usergroup_image')
                    ,description: _('discuss.usergroup_image_desc')
                    ,name: 'image'
                    ,anchor: '97%'
                },{
                    id: 'ug-image-preview'
                    ,html: ''
                    ,border: false
                },{
                    xtype: 'textfield'
                    ,inputType: 'file'
                    ,name: 'image'
                    ,anchor: '97%'
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
            },{
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
    initialized: false
    ,setup: function() {
        if (!this.config.record || this.initialized) return;
        var r = this.config.record;
        this.getForm().setValues(r);

        if (r.members) {
            var d = Ext.decode(r.members);
            var ug = Ext.getCmp('dis-grid-usergroup-members');
            if (ug) { ug.getStore().loadData(d); }
        }

        if (r.boards) {
            var b = Ext.decode(r.boards);
            var bg = Ext.getCmp('dis-grid-usergroup-boards');
            if (bg) { bg.getStore().loadData(b); }
        }

        Ext.getCmp('dis-usergroup-header').getEl().update('<h2>'+'UserGroup'+': '+r.name+'</h2>');

        if (r.badge) {
            Ext.get('ug-image-preview').update('<img src="'+r.badge+'?pq='+Math.floor(Math.random()*11)+'" alt="" />');
        }
        this.initialized = true;
    }
    ,beforeSubmit: function(o) {
        var d = {};
        var ug = Ext.getCmp('dis-grid-usergroup-members');
        if (ug) { d['members'] = ug.encode(); }

        var bg = Ext.getCmp('dis-grid-usergroup-boards');
        if (bg) { d['boards'] = bg.encode(); }
        
        Ext.apply(o.form.baseParams,d);
    }
    ,success: function(o) {
        if (!this.config['usergroup']) { 
            location.href = '?a='+Dis.request.a+'&action=usergroup/update&user='+o.result.object.id;
        } else {
            Ext.getCmp('dis-btn-save').setDisabled(false);
            var bg = Ext.getCmp('dis-grid-usergroup-members');
            if (bg) { bg.getStore().commitChanges(); }
            var ug = Ext.getCmp('dis-grid-usergroup-boards');
            if (ug) { ug.getStore().commitChanges(); }
        }
    }
});
Ext.reg('dis-panel-usergroup',Dis.panel.UserGroup);