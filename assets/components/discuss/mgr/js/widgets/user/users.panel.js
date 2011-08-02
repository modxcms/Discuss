
Dis.panel.Users = function(config) {
    config = config || {};
    Ext.apply(config,{
        title: _('discuss.users')
        ,autoHeight: true
        ,items: [{
            html: '<p>'+_('discuss.users.intro_msg')+'</p><br />'
            ,border: false
        },{
            xtype: 'dis-grid-users'
            ,autoHeight: true
            ,preventRender: true
        }]
    });
    Dis.panel.Users.superclass.constructor.call(this,config);
};
Ext.extend(Dis.panel.Users,MODx.Panel);
Ext.reg('dis-panel-users',Dis.panel.Users);


Dis.grid.Users = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-grid-users'
        ,url: Dis.config.connector_url
        ,baseParams: { action: 'mgr/user/getList' }
        ,save_action: 'mgr/user/updateFromGrid'
        ,fields: ['id','user','username','email','ip','last_active','posts']
        ,paging: true
        ,autosave: true
        ,remoteSort: true
        ,width: '95%'
        ,columns: [{
            header: _('id')
            ,dataIndex: 'user'
            ,sortable: true
            ,width: 90
        },{
            header: _('discuss.username')
            ,dataIndex: 'username'
            ,sortable: true
            ,width: 300
        },{
            header: _('email')
            ,dataIndex: 'email'
            ,sortable: true
            ,width: 300
        },{
            header: _('discuss.ip')
            ,dataIndex: 'ip'
            ,sortable: true
            ,width: 120
        },{
            header: _('discuss.posts')
            ,dataIndex: 'posts'
            ,sortable: true
            ,width: 90
        },{
            header: _('discuss.last_active')
            ,dataIndex: 'last_active'
            ,sortable: true
            ,width: 300
        }]
        ,tbar: ['->',{
            xtype: 'textfield'
            ,name: 'search'
            ,id: 'modx-user-search'
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
    Dis.grid.Users.superclass.constructor.call(this,config)
};
Ext.extend(Dis.grid.Users,MODx.grid.Grid,{

    getMenu: function() {
        var m = [];
        m.push({
            text: _('discuss.user_update')
            ,handler: this.updateUser
        });
        m.push('-');
        m.push({
            text: _('discuss.user_remove')
            ,handler: this.removeUser
        });
        this.addContextMenuItem(m);
    }
    ,removeUser: function() {
        MODx.msg.confirm({
            title: _('warning')
            ,text: _('discuss.user_remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/user/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.removeActiveRow,scope:this}
            }
        });
    }
    ,createUser: function() {
        location.href = '?a='+MODx.request.a+'&action=mgr/user/create';
    }
    ,updateUser: function() {
        var id = this.menu.record.id;
        location.href = '?a='+MODx.request.a+'&user='+id+'&action=mgr/user/update';
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
            action: 'mgr/user/getList'
    	};
        Ext.getCmp('modx-user-search').reset();
    	this.getBottomToolbar().changePage(1);
        this.refresh();
    }
});
Ext.reg('dis-grid-users',Dis.grid.Users);
