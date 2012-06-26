$(function() {
    DIS.UserIgnoreBoards.init();
});

DIS.UserIgnoreBoards = function() {
    return {
        init: function() {
            $('.dis-ignore-all').change(this.checkAll);
            $('.dis-category-li-simple input').change(this.checkCategory);
        }

        ,checkAll: function() {
            $('.dis-board-cb input').attr('checked',$(this).attr('checked'));
        }

        ,checkCategory: function() {
            var cls = $(this).attr('class');
            var id = cls.split('-')[1];
            $('.dis-category-'+id+' ul input').attr('checked',$(this).attr('checked'));
        }
    };
}();