var DISNewThread = {
    
    preview: function() {
        var f = $('#dis-new-thread-form');
        var p = f.serialize()+'&action=web/post/preview&ctx='+DIS.config.context;
        
        var a = $.extend({},DIS.baseAjax,{
            url: DIS.config.connector
            ,data: p
            ,success: function(r) {
                if (r.success == false) { DIS._showError(r.message); return false; }
                
                $('#dis-new-thread-preview').hide().html(r.message).fadeIn();
            }
        });
        $.ajax(a);
    }
};