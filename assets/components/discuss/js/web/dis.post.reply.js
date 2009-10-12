$(function() {
    DIS.ReplyPost.init();
});
DIS.ReplyPost = function() {
    var attachments = 1;
    
    return {
        init: function() {
            $('.dis-reply-post-preview').click(this.preview);
            $('.dis-post-title').click(this.togglePost);
            $('.dis-post-author').click(this.toggleAuthor);
            $('.dis-reply-post-add-attachment').click(this.addAttachment);
        }
        
        ,preview: function() {
            var f = $('#dis-reply-post-form');
            var p = f.serialize()+'&action=web/post/preview&ctx='+DIS.config.context;
            
            var a = $.extend({},DIS.baseAjax,{
                url: DIS.config.connector
                ,data: p
                ,success: function(r) {
                    if (r.success == false) { DIS._showError(r.message); return false; }
                    
                    $('#dis-reply-post-preview').hide().html(r.message).fadeIn();
                }
            });
            $.ajax(a);
        }        
        
        ,togglePost: function() {
            var p = $(this).attr('post');
            $('#dis-board-post-'+p).find('ol').slideToggle();
            $('#dis-thread-ct-'+p).slideToggle();
        }
        
        ,toggleAuthor: function() {
            var p = $(this).attr('post');
            $(this).find('.dis-author').slideToggle();
        }
        
        ,addAttachment: function() {
            var d = $('#dis-attachments');
            var i = attachments+1;
            var tpl = '<label><span>&nbsp;</span></label><input type="file" name="attachment'+i+'" /><br class="clear" />';
            
            d.append(tpl);
            attachments = attachments+1;
            return false;
        }
    }
}();