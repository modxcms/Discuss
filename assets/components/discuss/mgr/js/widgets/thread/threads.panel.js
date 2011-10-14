Dis.panel.Threads = function(config) {
    config = config || {};
    Ext.apply(config,{
        title: _('discuss.threads')
        ,autoHeight: true
        ,items: [{
            html: '<p>'+_('discuss.threads.intro_msg')+'</p>'
            ,border: false
            ,bodyCssClass: 'panel-desc'
        },{
            xtype: 'dis-grid-threads'
            ,autoHeight: true
            ,preventRender: true
            ,cls: 'main-wrapper'
        }]
    });
    Dis.panel.Threads.superclass.constructor.call(this,config);
};
Ext.extend(Dis.panel.Threads,MODx.Panel);
Ext.reg('dis-panel-threads',Dis.panel.Threads);


Dis.grid.Threads = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-grid-threads'
        ,url: Dis.config.connectorUrl
        ,baseParams: { action: 'mgr/thread/getList' }
        ,save_action: 'mgr/thread/updateFromGrid'
        ,fields: ['id','title','url']
        ,paging: true
        ,autosave: true
        ,remoteSort: true
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 120
        },{
            header: _('discuss.title')
            ,dataIndex: 'title'
            ,sortable: true
            ,width: 200
        }]
        ,tbar: ['->',{
            xtype: 'textfield'
            ,name: 'search'
            ,id: 'dis-threads-search'
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
    Dis.grid.Threads.superclass.constructor.call(this,config)
};
Ext.extend(Dis.grid.Threads,MODx.grid.Grid,{

    getMenu: function() {
        var m = [];
        m.push({
            text: _('discuss.thread_remove')
            ,handler: this.removeThread
        });
        this.addContextMenuItem(m);
    }
    ,removeThread: function() {
        MODx.msg.confirm({
            title: _('warning')
            ,text: _('discuss.activity_log_remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/thread/remove'
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
            action: 'mgr/thread/getList'
    	};
        Ext.getCmp('dis-threads-search').reset();
    	this.getBottomToolbar().changePage(1);
        this.refresh();
    }
});
Ext.reg('dis-grid-threads',Dis.grid.Threads);
