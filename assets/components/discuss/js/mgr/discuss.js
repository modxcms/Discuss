var Dis = function(config) {
    config = config || {};
    Dis.superclass.constructor.call(this,config);
};
Ext.extend(Dis,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {}
});
Ext.reg('discuss',Dis);

var Dis = new Dis();