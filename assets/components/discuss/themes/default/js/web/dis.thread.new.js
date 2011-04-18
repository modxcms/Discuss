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
            var p = f.serialize()+'&action=thread/preview';

            var a = $.extend({},DIS.baseAjax,{
                url: DIS.url
                ,async: false
                ,data: p
            });
            var a = $.ajax(a);
            $('#dis-new-thread-preview').hide().html(a.responseText).fadeIn();
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