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
            $('.dis-add-attachment').click(this.addAttachment);
        }
        
        ,preview: function() {
            var f = $('#dis-reply-post-form');
            var p = f.serialize()+'&action=thread/preview';

            var a = $.extend({},DIS.baseAjax,{
                url: DIS.url
                ,async: false
                ,data: p
                ,type: 'POST'
            });
            var a = $.ajax(a);
            $('#dis-message-preview').hide().html(a.responseText).fadeIn();
            if (SyntaxHighlighter) { SyntaxHighlighter.highlight(); }
        }
        
        ,togglePost: function() {
            var p = $(this).attr('post');
            $('#dis-board-post-'+p).find('ol').slideToggle();
            $('#dis-thread-ct-'+p).slideToggle();
        }
        
        ,toggleAuthor: function() {
            $(this).find('.dis-sig-ct').slideToggle();
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
    }
}();