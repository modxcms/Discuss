$(function() {
   $('.dis-category-li').click(DISBoard.toggleCategory);
});
var DISBoard = {
    toggleCategory: function(id) {
        id = $(this).attr('id');
        $(this).parent().children('li.dis-board-li.'+id).slideToggle();
    }
};