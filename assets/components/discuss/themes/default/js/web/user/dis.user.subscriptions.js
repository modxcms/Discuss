$(function() {
    DIS.UserSubscriptions.init();
});

DIS.UserSubscriptions = function() {
    return {
        init: function() {
            $('.dis-remove-all').change(this.checkAll);
        }
        
        ,checkAll: function() {
            $('.dis-remove-cb').attr('checked',$(this).attr('checked'));
        }
    };
}();