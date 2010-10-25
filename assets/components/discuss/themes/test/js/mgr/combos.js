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