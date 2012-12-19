$(function() {
    DIS.Search.init();
});
DIS.Search = function() {
    
    return {
        init: function() {
            //$('.dis-search-result').toggle();
            $('.dis-search-parent-result').click(this.toggleThread);
            $(".date-picker").datepicker();
        }
        
        ,toggleThread: function() {
            var id = $(this).attr('class').split(' ')[1].replace(/dis-parent-result-/,'');
            $(this).parent().find('.dis-result-'+id).toggle();
            var tog = $(this).find('.dis-toggle');
            tog.html(tog.html() == '+' ? '-' : '+'); 
        }
        
    };
}();