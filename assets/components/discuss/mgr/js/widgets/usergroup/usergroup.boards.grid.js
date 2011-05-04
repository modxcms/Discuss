
Dis.grid.UserGroupBoards = function(config) {
    config = config || {};
    var tt = new Ext.ux.grid.CheckColumn({
        header: _('discuss.access')
        ,dataIndex: 'access'
        ,width: 40
        ,sortable: false
    });
    Ext.applyIf(config,{
        id: 'dis-grid-usergroup-boards'
        ,url: Dis.config.connector_url
        ,baseParams: {
            action: 'mgr/usergroup/board/getlist'
            ,user: config.user
        }
        ,action: 'mgr/usergroup/board/getlist'
        ,fields: ['id','name','access','category','category_name']
        ,data: []
        ,autoHeight: true
        ,grouping: false
        ,groupBy: 'category_name'
        ,plugins: tt
        ,columns: [{
            header: _('name')
            ,dataIndex: 'name'
            ,width: 250
        },{
            header: _('category')
            ,dataIndex: 'category_name'
            ,width: 250
        },tt]
    });
    Dis.grid.UserGroupBoards.superclass.constructor.call(this,config);
    this.propRecord = Ext.data.Record.create([{name: 'id'},{name:'name'},{name:'access'}]);
};
Ext.extend(Dis.grid.UserGroupBoards,MODx.grid.LocalGrid);
Ext.reg('dis-grid-usergroup-boards',Dis.grid.UserGroupBoards);