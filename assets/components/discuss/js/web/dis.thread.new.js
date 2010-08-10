$(function() {
    DIS.NewThread.init();
});
DIS.NewThread = function() {
    var attachments = 1;
    
    return {
        init: function() {
            $('.dis-new-thread-preview').click(this.preview);
            $('.dis-new-thread-add-attachment').click(this.addAttachment);
            
        }
        ,preview: function() {
            var f = $('#dis-new-thread-form');
            var p = f.serialize()+'&action=web/post/preview&ctx='+DIS.config.context;
            
            var a = $.extend({},DIS.baseAjax,{
                url: DIS.config.connector
                ,data: p
                ,success: function(r) {
                    if (r.success == false) { DIS._showError(r.message); return false; }
                    
                    $('#dis-new-thread-preview').hide().html(r.message).fadeIn();
                    return true;
                }
            });
            $.ajax(a);
        }   
        
        ,addAttachment: function() {
            var d = $('#dis-attachments');
            var i = attachments+1;
            if (i > DIS.config.attachments_max_per_post) return false;
            var tpl = '<label><span>&nbsp;</span></label><input type="file" name="attachment'+i+'" /><br class="clear" />';
            
            d.append(tpl);
            attachments = attachments+1;
            return false;
        }
    };
}();