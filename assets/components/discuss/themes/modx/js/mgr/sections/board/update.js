Ext.onReady(function() {
    MODx.load({ xtype: 'dis-page-board-update'});
});

Dis.page.UpdateBoard = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        formpanel: 'dis-panel-board'
        ,buttons: [{
            text: _('save')
            ,id: 'dis-btn-save'
            ,process: 'mgr/board/update'
            ,method: 'remote'
            ,keys: [{
                key: 's'
                ,alt: true
                ,ctrl: true
            }]
        },'-',{
            text: _('cancel')
            ,id: 'dis-btn-back'
            ,handler: function() {
                location.href = '?a='+Dis.request.a+'&action=home';
            }
            ,scope: this
        }]
        ,components: [{
            xtype: 'dis-panel-board'
            ,board: Dis.request.board
            ,renderTo: 'dis-panel-board-div'
        }]
    }); 
    Dis.page.UpdateBoard.superclass.constructor.call(this,config);
};
Ext.extend(Dis.page.UpdateBoard,MODx.Component);
Ext.reg('dis-page-board-update',Dis.page.UpdateBoard);