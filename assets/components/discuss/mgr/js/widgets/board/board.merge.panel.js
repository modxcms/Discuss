Dis.panel.MergeBoard = function(config) {
    config = config || {};
    config.id = 'dis-panel-board';
    Ext.applyIf(config,{
        url: Dis.config.connector_url
        ,baseParams: {
            action: 'mgr/board/merge'
            ,register: 'discuss'
            ,topic: '/merge' + Dis.record.id + '/'
        }
        ,cls: 'container form-with-labels'
        ,items: [{
            html: '<h2>'+_('discuss.board_merge')+'</h2>'
            ,border: false
            ,id: 'dis-board-header'
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs',
            defaults: {
                autoHeight: true
                ,border: true
				,bodyCssClass: 'tab-panel-wrapper'
            },
            items: [{
                title: _('discuss.board_merge'),
                items: [{
                    layout: 'column'
                    ,cls: 'main-wrapper'
                    ,border: false
                    ,anchor: '100%'
                    ,defaults: {
                        layout: 'form'
                        ,labelAlign: 'top'
                        ,anchor: '100%'
                        ,border: false
                        ,defaults: {
                            border: false
                        }
                    }
                    ,items: [{
                        columnWidth: .5,
                        defaults: {
                            anchor: '100%'
                        },
                        items: [{
                            xtype: 'statictextfield',
                            name: 'name',
                            fieldLabel: _('discuss.board_merge.name')
                        },{
                            xtype: 'hidden',
                            name: 'id'
                        },{
                            xtype: 'dis-combo-boards',
                            name: 'merge_into',
                            fieldLabel: _('discuss.board_merge.merge_into')
                        },{
                            xtype: 'dis-combo-merge-boardaction',
                            name: 'boardaction',
                            fieldLabel: _('discuss.board_merge.boardaction')
                        }]
                    },{
                        columnWidth: .5,
                        items: [{
                            xtype: 'panel',
                            height: 275,
                            scrollable: true,
                            autoScroll: true,
                            hidden: true,
                            id: config.id + '-console-container',
                            items: [{
                                xtype: 'panel',
                                id: config.id + '-console',
                                cls: 'x-form-text modx-console-text'
                            }]
                        }]
                    }]
                }]
            }]
        }]
        ,listeners: {
            'setup': {fn:this.setup,scope:this}
            ,'success': {fn:this.success,scope:this}
        }
    });
    Dis.panel.MergeBoard.superclass.constructor.call(this,config);
};
Ext.extend(Dis.panel.MergeBoard,MODx.FormPanel,{
    running: false,
    setup: function() {
        this.getForm().setValues(Dis.record);
    },

    startMerge: function() {
        var f = this.getForm();
        if (!f.isValid() || !this.fireEvent('beforeSubmit',f.getValues())) {
            MODx.msg.alert('Invalid','Please complete the form before starting a merge.');
            return;
        }

        Ext.getCmp('dis-btn-merge').disable();

        var body = Ext.getCmp(this.config.id + '-console');
        body.el.dom.innerHTML = '';
        var bodyContainer = Ext.getCmp(this.id + '-console-container');
        bodyContainer.show();

        this.provider = new Ext.direct.PollingProvider({
            type:'polling'
            ,url: MODx.config.connectors_url+'system/index.php'
            ,interval: 1000
            ,baseParams: {
                action: 'console'
                ,register: 'discuss'
                ,topic: '/merge' + Dis.record.id + '/'
                ,show_filename: this.config.show_filename || 0
                ,format: this.config.format || 'html_log'
            }
        });
        Ext.Direct.addProvider(this.provider);
        Ext.Direct.on('message', function(e,p) {
            body.el.insertHtml('beforeEnd',e.data);
            if (e.complete) {
                this.provider.disconnect();
                Ext.getCmp('dis-btn-merge').enable();
            }
        },this);
        body.el.insertHtml('beforeEnd',_('discuss.board_merge.start') + '<br />');

        this.submit(false);
    },

    submit: function(close) {
        var f = this.getForm();
        if (f.isValid() && this.fireEvent('beforeSubmit',f.getValues())) {
            f.submit();
        }
    }
});
Ext.reg('dis-panel-board',Dis.panel.MergeBoard);
