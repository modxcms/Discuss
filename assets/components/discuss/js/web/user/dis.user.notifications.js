$(function() {
    DIS.UserNotifications.init();
});

DIS.UserNotifications = function() {    
    return {
        init: function() {
            $('.dis-remove-all').change(this.checkAll);
        }
        
        ,checkAll: function() {
            var v = $('.dis-remove-cb').attr('checked',$(this).attr('checked'));
        }
    };
}();