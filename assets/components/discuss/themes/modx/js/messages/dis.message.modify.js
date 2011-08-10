DIS.DISModifyMessage = function() {
    var attachments = 1;
    return {
        init: function(o) {
            $('.dis-message-write').click(this.message);
            $('.dis-add-attachment').click(this.addAttachment);
            $('.dis-remove-attachment').click(this.removeAttachment);
            $('.dis-modify-message-preview').click(this.preview);
            attachments = o.attachments || 1;
        }
    
        ,preview: function() {
            var f = $('#dis-modify-message-form');
            var p = f.serialize()+'&action=thread/preview';

            var a = $.extend({},DIS.baseAjax,{
                url: DIS.url
                ,async: false
                ,data: p
                ,type: 'POST'
            });
            var a = $.ajax(a);
            $('#dis-modify-message-preview').hide().html(a.responseText).fadeIn();
            if (SyntaxHighlighter) { SyntaxHighlighter.highlight(); }

            $('.dis-message-write').removeClass('selected');
            $('.dis-modify-message-preview').addClass('selected');
            return false;
        }

        ,message: function() {
            $('.dis-modify-message-preview').removeClass('selected');
            $('.dis-message-write').addClass('selected');
            $('#dis-modify-message-preview').fadeOut();
            return false;        
        }

        ,addAttachment: function() {
            var d = $('#dis-attachments');
            var i = attachments+1;
            if (i > DIS.config.attachments_max_per_post) return false;
            var tpl = '<label><span>&nbsp;</span></label><input type="file" name="attachment'+i+'" /><br class="clearfix" />';

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