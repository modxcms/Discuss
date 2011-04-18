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