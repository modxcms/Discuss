DIS.DISModifyPost = function() {
    var attachments = 1;
    return {
        init: function(o) {
            $('.dis-add-attachment').click(this.addAttachment);
            $('.dis-remove-attachment').click(this.removeAttachment);
            $('.dis-modify-post-preview-btn').click(this.preview);
            $('.dis-message-write').click(this.message);
            attachments = o.attachments || 1;
        }
    
        ,preview: function() {
            var f = $('#dis-modify-post-form');
            var p = f.serialize()+'&action=thread/preview';

            var a = $.extend({},DIS.baseAjax,{
                url: DIS.url
                ,async: false
                ,data: p
            });
            var a = $.ajax(a);
            $('#dis-modify-post-preview').hide().html(a.responseText).fadeIn(80);
            if (SyntaxHighlighter) { SyntaxHighlighter.highlight(); }

            $('.dis-message-write').removeClass('selected');
            $('.dis-modify-post-preview-btn').addClass('selected');
            $('#overlay-20').fadeIn();
            return false;
        }

        ,message: function() {
            $('.dis-modify-post-preview-btn').removeClass('selected');
            $('.dis-message-write').addClass('selected');
            $('#dis-modify-post-preview').fadeOut(80);
            $('#overlay-20').fadeOut();
            return false;        
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

        ,removeAttachment: function() {
            var li = $(this).parent();
            li.remove();
        }
    };
}();