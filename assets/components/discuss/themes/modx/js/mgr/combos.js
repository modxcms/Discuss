Dis.combo.Category = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        name: 'category'
        ,hiddenName: 'category'
        ,displayField: 'name'
        ,valueField: 'id'
        ,fields: ['id','name']
        ,forceSelection: true
        ,typeAhead: false
        ,editable: false
        ,allowBlank: false
        ,listWidth: 300
        ,url: Dis.config.connector_url
        ,baseParams: {
            action: 'mgr/category/getList'
            ,combo: true
        }
    });
    Dis.combo.Category.superclass.constructor.call(this,config);
};
Ext.extend(Dis.combo.Category,MODx.combo.ComboBox);
Ext.reg('dis-combo-category',Dis.combo.Category);

Dis.combo.User = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        name: 'user'
        ,hiddenName: 'user'
        ,displayField: 'username'
        ,valueField: 'id'
        ,fields: ['id','username','email']
        ,forceSelection: true
        ,typeAhead: true
        ,editable: true
        ,allowBlank: false
        ,listWidth: 300
        ,url: Dis.config.connector_url
        ,baseParams: {
            action: 'mgr/user/getList'
            ,combo: true
        }
    });
    Dis.combo.User.superclass.constructor.call(this,config);
};
Ext.extend(Dis.combo.User,MODx.combo.ComboBox);
Ext.reg('dis-combo-user',Dis.combo.User);

Dis.combo.MinimumPostLevel = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [[_('discuss.member'),9999],[_('discuss.moderator'),1],[_('discuss.admin'),0]]
        })
        ,name: 'minimum_post_level'
        ,hiddenName: 'minimum_post_level'
        ,width: 200
        ,displayField: 'd'
        ,valueField: 'v'
        ,mode: 'local'
        ,triggerAction: 'all'
        ,editable: false
        ,pageSize: 20
        ,selectOnFocus: false
        ,preventRender: true
    });
    Dis.combo.MinimumPostLevel.superclass.constructor.call(this,config);
};
Ext.extend(Dis.combo.MinimumPostLevel,MODx.combo.ComboBox);
Ext.reg('dis-combo-minimum-post-level',Dis.combo.MinimumPostLevel);


Dis.combo.BoardStatus = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [[_('discuss.active'),1],[_('discuss.inactive'),0],[_('discuss.archived'),2]]
        })
        ,name: 'status'
        ,hiddenName: 'status'
        ,width: 200
        ,displayField: 'd'
        ,valueField: 'v'
        ,mode: 'local'
        ,triggerAction: 'all'
        ,editable: false
        ,pageSize: 20
        ,selectOnFocus: false
        ,preventRender: true
    });
    Dis.combo.BoardStatus.superclass.constructor.call(this,config);
};
Ext.extend(Dis.combo.BoardStatus,MODx.combo.ComboBox);
Ext.reg('dis-combo-board-status',Dis.combo.BoardStatus);
