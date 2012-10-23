$(function() {
    $('.dis-poll-refresh').hide();
    DIS.Thread.init();

    /* Question/Discussion selectors (class_key input) */
	$(".m-dis-thread-type li label").click(function(){
        var isActive = $(this).parent('li').hasClass('current');
        if (!isActive) {
            var allInfos = $('.dis-thread-info li:visible'),
                allLabels = $('.m-dis-thread-type li'),
                clickTarget = $(this).parent('li'),
                target = $(clickTarget.data('target'));

            allInfos.fadeOut('slow',function() {
                allInfos.removeClass('current');
                allLabels.removeClass('current');
                clickTarget.addClass('current');
                target.fadeIn('slow',function() {
                    target.addClass('current');
                });
            });
        }
    });
    /* Make sure the class_key radio button triggers the above click handler
    on loading the page.
     */
    $('input:radio[name=class_key]:checked').parent('label').click()
});

DIS.Thread = function() {
    var postCount = DIS.config.postCount;
    var attachments = 1;
    
    return {
        init: function() {
            $('#dis-preview-btn').click(this.preview);
            $('.dis-message-write').click(this.message);
            $("#dis-message-preview").delegate(".dis-message-cancel", "click", this.message);
            $('.dis-post-title').click(this.togglePost);
            $('.dis-post-author').click(this.toggleAuthor);
            $('.dis-post-remove').click(this.removePost);
            $('.quick-reply').click(this.quickReply);
            $('.dis-add-attachment').click(this.addAttachment);
            $('.dis-post-reply').click(this.quickQuote);
        }
        
        ,preview: function(event) {
            event.preventDefault();
            var f = $('.dis-thread-form');
            var p = f.serialize()+'&action=thread/preview';

            var a = $.extend({},DIS.baseAjax,{
                url: DIS.url
                ,async: false
                ,data: p
                ,type: 'POST'
            });
            var a = $.ajax(a);
            $('#dis-message-preview').hide().html(a.responseText).fadeIn(80);
            if (SyntaxHighlighter) { SyntaxHighlighter.highlight(); }
            
            $('.dis-message-write').removeClass('selected');
            $('.dis-preview').addClass('selected');
            $('#overlay-20').fadeIn();
            return false;
        }
        
        ,message: function() {
            $('.dis-preview').removeClass('selected');
            $('.dis-message-write').addClass('selected');
            $('#dis-message-preview').fadeOut(80);
            $('#overlay-20').fadeOut();
            return false;        
        }

		,quickReply: function() {
			$.scrollTo($('.preview_toggle'),500);
			$('#dis-thread-message').focus();
			return false;
		},

        quickQuote: function(event) {
            event.preventDefault();
            var msgObj = $('#dis-thread-message'),
                val = msgObj.val(),
                data = $(this).parents('li.dis-post').data();

            /* To convert our htmlentities() processed message, we throw it in a div
                and request the innerHTML of that.
             */
            var temp = document.createElement("div");
            temp.innerHTML = data.message;
            data.message = temp.innerHTML;

            val = val + ((val.length > 0) ? '\n\n' : '') + '[quote author='+data.author+' date='+data.date+']'+data.message+'[/quote]';
            msgObj.val(val).trigger('autosize');

            $('html,body').animate({'scrollTop': $('.dis-thread-form').position().top}, 500, function() {
                $('#dis-thread-message').focus();
            });

            return false;
        }

        ,pollPosts: function() {
             var a = $.extend({},DIS.baseAjax,{
                url: DIS.config.connector
                ,data: {
                    action: 'web/post/pollRefresh'
                    ,post: 1
                    ,ctx: DIS.config.context
                }
                ,success: function(r) {
                    if (r.success == false) { DIS._showError(r.message); return false; }
                    
                    var diff = parseInt(r.message) - parseInt(postCount);
                    if (diff != 0) {
                        DIS.Thread.displayRefreshMessage(diff);
                        postCount = r.message;
                    }
                    setTimeout('DIS.Thread.pollPosts();',DIS.config.pollingInterval);
                    return true;
                }
            });
            $.ajax(a);
        }

        ,displayRefreshMessage: function(d) {
            /* TODO: move html to a chunk */
            $('.dis-poll-refresh').html('There have been '+d+' new posts since you last reloaded. Please ' +
                    '<a href="javascript:void(0);" onclick="DIS.Thread.reloadThread();">click here</a> ' +
                    'to refresh.').fadeIn();
        }
        
        ,reloadThread: function() {
            var a = $.extend({},DIS.baseAjax,{
                url: DIS.config.connector
                ,data: {
                    action: 'web/post/loadThread'
                    ,post: 1
                    ,ctx: DIS.config.context
                }
                ,success: function(r) {
                    if (r.success == false) { DIS._showError(r.message); return false; }
                    
                    $('#dis-thread').hide().html(r.message).slideDown();
                    $('.dis-poll-refresh').fadeOut();
                    return true;
                }
            });
            $.ajax(a);
        }
        
        ,togglePost: function() {
            var p = $(this).attr('post');
            $('#dis-board-post-'+p).find('ol').slideToggle();
            $('#dis-thread-ct-'+p).slideToggle();
            $('#dis-post-author-'+p).slideToggle();
        }
        
        ,toggleAuthor: function() {
            $(this).find('.dis-sig-ct').slideToggle();
        }
        
    
        ,showReplyForm: function() {
            var id = $(this).closest('.dis-post').attr('id');
            id = id.replace(/dis-post-/,'');
            
            var a = $.extend({},DIS.baseAjax,{
                url: DIS.config.connector
                ,data: {
                    action: 'web/post/replyForm'
                    ,post: id
                    ,ctx: DIS.config.context
                }
                ,success: function(r) {
                    if (r.success == false) { DIS._showError(r.message); return false; }
                    
                    $('#dis-post-reply-'+id).hide().html(r.message).slideDown();
                    $('#dis-reply-form-'+id+' textarea').focus();
                    return true;
                }
            });
            $.ajax(a);
        }
        ,hideReplyForm: function(id) {
            $('#dis-post-reply-'+id).slideUp();
        }
        ,postReply: function(id) {
            var f = $('#dis-reply-form-'+id);
            var p = f.serialize()+'&action=web/post/reply&ctx='+DIS.config.context;
            
            var a = $.extend({},DIS.baseAjax,{
                url: DIS.config.connector
                ,data: p
                ,success: function(r) {
                    if (r.success == false) { DIS._showError(r.message); return false; }
                    var ol = $('#dis-board-post-'+id).children('ol');
                    if (ol.length > 0) {
                        ol.append(r.message);
                    } else {
                        $('#dis-board-post-'+id).append('<ol class="dis-board-thread">'+r.message+'</ol>');
                    }
                    var ct = parseInt($('.dis-author-post-count').html());
                    $('.dis-author-post-count').html((ct+1));
                    DIS.Thread.hideReplyForm(id);
                    DIS.Thread.init();
                    return true;
                }
            });
            $.ajax(a);
        }
        
        ,removePost: function() {
            var p = $.q($(this).attr('href'));
            p.id = $(this).closest('.dis-post').attr('id');
            p.id = p.id.replace(/dis-post-/,'');
            return confirm('Are you sure you want to remove this post?');
        }

        ,addAttachment: function() {
            var d = $('#dis-attachments');
            var i = attachments+1;
            if (i > DIS.config.attachments_max_per_post) return false;
            var tpl = '<input type="file" name="attachment'+i+'" />';

            d.append(tpl);
            attachments = attachments+1;
            return false;
        }
    };
}();
