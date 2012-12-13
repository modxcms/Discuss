Ext.onReady(function() {
    MODx.load({ xtype: 'dis-page-board-merge'});
});

Dis.page.MergeBoard = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        formpanel: 'dis-panel-board'
        ,buttons: [{
            text: _('discuss.board_merge')
            ,id: 'dis-btn-merge'
            ,handler: this.startMerge
            ,keys: [{
                key: 's'
                ,alt: true
                ,ctrl: true
            }]
        },'-',{
            text: _('cancel')
            ,id: 'dis-btn-back'
            ,handler: function() {
                location.href = '?a='+Dis.request.a;
            }
            ,scope: this
        }]
        ,components: [{
            xtype: 'dis-panel-board'
            ,board: Dis.request.board
            ,renderTo: 'dis-panel-board-div'
            ,record: Dis.record
        }]
    }); 
    Dis.page.MergeBoard.superclass.constructor.call(this,config);
};
Ext.extend(Dis.page.MergeBoard,MODx.Component,{
    startMerge: function() {
        Ext.getCmp('dis-panel-board').startMerge();
    }
});
Ext.reg('dis-page-board-merge',Dis.page.MergeBoard);
