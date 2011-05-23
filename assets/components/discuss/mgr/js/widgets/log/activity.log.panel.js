
Dis.panel.ActivityLog = function(config) {
    config = config || {};
    Ext.apply(config,{
        title: _('discuss.activity_log')
        ,autoHeight: true
        ,items: [{
            html: '<p>'+_('discuss.activity_log.intro_msg')+'</p><br />'
            ,border: false
        },{
            xtype: 'dis-grid-activity-log'
            ,autoHeight: true
            ,preventRender: true
        }]
    });
    Dis.panel.ActivityLog.superclass.constructor.call(this,config);
};
Ext.extend(Dis.panel.ActivityLog,MODx.Panel);
Ext.reg('dis-panel-activity-log',Dis.panel.ActivityLog);


Dis.grid.ActivityLog = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-grid-activity-log'
        ,url: Dis.config.connectorUrl
        ,baseParams: { action: 'mgr/log/activity/getList' }
        ,save_action: 'mgr/log/activity/updateFromGrid'
        ,fields: ['id','user','username','createdon','ip','action','url']
        ,paging: true
        ,autosave: true
        ,remoteSort: true
        ,width: '95%'
        ,columns: [{
            header: _('discuss.action')
            ,dataIndex: 'action'
            ,sortable: true
            ,width: 120
        },{
            header: _('discuss.username')
            ,dataIndex: 'username'
            ,sortable: true
            ,width: 150
        },{
            header: _('discuss.ip')
            ,dataIndex: 'ip'
            ,sortable: true
            ,width: 120
        },{
            header: _('discuss.occurredon')
            ,dataIndex: 'createdon'
            ,sortable: true
            ,width: 120
        }]
        ,tbar: ['->',{
            xtype: 'textfield'
            ,name: 'search'
            ,id: 'modx-activity-log-search'
            ,emptyText: _('search_ellipsis')
            ,listeners: {
                'change': {fn: this.search, scope: this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this.getValue());
                            this.blur();
                            return true;}
                        ,scope: cmp
                    });
                },scope:this}
            }
        },{
            xtype: 'button'
            ,id: 'modx-filter-clear'
            ,text: _('filter_clear')
            ,listeners: {
                'click': {fn: this.clearFilter, scope: this}
            }
        }]
    });
    Dis.grid.ActivityLog.superclass.constructor.call(this,config)
};
Ext.extend(Dis.grid.ActivityLog,MODx.grid.Grid,{

    getMenu: function() {
        var m = [];
        m.push({
            text: _('discuss.activity_log_remove')
            ,handler: this.removeLogEntry
        });
        this.addContextMenuItem(m);
    }
    ,removeLogEntry: function() {
        MODx.msg.confirm({
            title: _('warning')
            ,text: _('discuss.activity_log_remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/log/activity/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.removeActiveRow,scope:this}
            }
        });
    }
    ,search: function(tf,newValue,oldValue) {
        var nv = newValue || tf;
        this.getStore().baseParams.query = Ext.isEmpty(nv) || Ext.isObject(nv) ? '' : nv;
        this.getBottomToolbar().changePage(1);
        this.refresh();
        return true;
    }
    ,clearFilter: function() {
    	this.getStore().baseParams = {
            action: 'mgr/log/activity/getList'
    	};
        Ext.getCmp('modx-activity-log-search').reset();
    	this.getBottomToolbar().changePage(1);
        this.refresh();
    }
});
Ext.reg('dis-grid-activity-log',Dis.grid.ActivityLog);
