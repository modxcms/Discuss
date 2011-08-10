$(function() {
    DIS.NewMessage.init();
});
DIS.NewMessage = function() {
    var attachments = 1;
    
    return {
        init: function() {
            $('.dis-reply-post-preview').click(this.preview);
            $('.dis-message-write').click(this.message);
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
            });
            var a = $.ajax(a);
            $('#dis-reply-post-preview').hide().html(a.responseText).fadeIn();
            if (SyntaxHighlighter) { SyntaxHighlighter.highlight(); }

            $('.dis-message-write').removeClass('selected');
            $('.dis-reply-post-preview').addClass('selected');
            return false;
        }

        ,message: function() {
            $('.dis-reply-post-preview').removeClass('selected');
            $('.dis-message-write').addClass('selected');
            $('#dis-reply-post-preview').fadeOut();
            return false;        
        }

        ,cancel: function() {
            $('#dis-reply-post-preview').fadeOut('slow');
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