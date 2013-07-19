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
        },
        mode : 'local'
    });
    Dis.combo.Category.superclass.constructor.call(this,config);
};
Ext.extend(Dis.combo.Category,MODx.combo.ComboBox);
Ext.reg('dis-combo-category',Dis.combo.Category);

Dis.combo.Boards = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        hiddenName: config.name
        ,displayField: 'name'
        ,valueField: 'id'
        ,fields: ['id','name','category_name']
        ,forceSelection: true
        ,typeAhead: true
        ,editable: true
        ,allowBlank: false
        ,listWidth: 400
        ,url: Dis.config.connector_url
        ,baseParams: {
            action: 'mgr/board/getlist'
            ,combo: true
        }
        ,pageSize: 20
        ,tpl: new Ext.XTemplate('<tpl for="."><div class="x-combo-list-item">{name} <span style="color: #999; font-size: 90%;">{category_name}</span></div></tpl>')
    });
    Dis.combo.Boards.superclass.constructor.call(this,config);
};
Ext.extend(Dis.combo.Boards,MODx.combo.ComboBox);
Ext.reg('dis-combo-boards',Dis.combo.Boards);

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

Dis.combo.RTL = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [[_('discuss.ltr'),0],[_('discuss.rtl'),1]]
        })
        ,name: 'rtl'
        ,hiddenName: 'rtl'
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
    Dis.combo.RTL.superclass.constructor.call(this,config);
};
Ext.extend(Dis.combo.RTL,MODx.combo.ComboBox);
Ext.reg('dis-combo-rtl',Dis.combo.RTL);



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

Dis.combo.MergeBoardAction = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [[_('discuss.board_merge.remove'),'remove'],
                [_('discuss.board_merge.deactivate'),'deactivate'],
                [_('discuss.board_merge.archive'),'archive'],
                [_('discuss.board_merge.nothing'),'nothing']
            ]
        })
        ,name: config.name || 'boardaction'
        ,hiddenName: config.name
        ,width: 200
        ,displayField: 'd'
        ,valueField: 'v'
        ,mode: 'local'
        ,triggerAction: 'all'
        ,editable: false
        ,pageSize: 20
        ,selectOnFocus: false
        ,preventRender: true
        ,value: 'nothing'
    });
    Dis.combo.MinimumPostLevel.superclass.constructor.call(this,config);
};
Ext.extend(Dis.combo.MergeBoardAction,MODx.combo.ComboBox);
Ext.reg('dis-combo-merge-boardaction',Dis.combo.MergeBoardAction);
