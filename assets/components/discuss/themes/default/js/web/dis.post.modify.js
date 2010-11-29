DIS.DISModifyPost = function() {
    var attachments = 1;
    return {
        init: function(o) {
            $('.dis-add-attachment').click(this.addAttachment);
            $('.dis-remove-attachment').click(this.removeAttachment);
            attachments = o.attachments || 1;
        }
    
        ,preview: function() {
            var f = $('#dis-modify-post-form');
            var p = f.serialize()+'&action=web/post/preview&ctx='+DIS.config.context;

            var a = $.extend({},DIS.baseAjax,{
                url: DIS.config.connector
                ,data: p
                ,success: function(r) {
                    if (r.success == false) { DIS._showError(r.message); return false; }

                    $('#dis-modify-post-preview').hide().html(r.message).fadeIn();
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

        ,removeAttachment: function() {
            var li = $(this).parent();
            li.remove();
        }
    };
}();