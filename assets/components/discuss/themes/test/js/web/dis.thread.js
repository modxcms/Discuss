$(function() {
    $('.dis-poll-refresh').hide();
    DIS.Thread.init();
    //setTimeout('DIS.Thread.pollPosts();',DIS.config.pollingInterval);
});

DIS.Thread = function() {
    var postCount = DIS.config.postCount;
    
    return {
        init: function() {
            $('.post-title').click(this.togglePost);
            $('.dis-post-remove').click(this.removePost);
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
			div = $(this).parent('div');			
			div.siblings('ol').slideToggle();
			$(this).siblings('div.content-wrap').slideToggle();
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
                    
            var s = confirm('Are you sure you want to remove this post?');
            if (s) {
                var a = $.extend({},DIS.baseAjax,{
                    url: DIS.config.connector
                    ,data: {
                        action: 'web/post/remove'
                        ,post: p.id
                    }
                    ,success: function(r) {
                        if (r.success == false) { DIS._showError(r.message); return false; }
                        
                        var ol = $('#dis-board-post-'+p.id).parent('ol');
                        var lis = ol.children('li');
                        if (lis.length == 1) {
                            ol.fadeOut().remove();
                        } else {
                            $('#dis-board-post-'+p.id).fadeOut().remove();
                        }
                        var ct = parseInt($('.dis-author-post-count').html());
                        $('.dis-author-post-count').html((ct-1));
                        if (p['parent'] == 0) {
                            location.href = p.url;
                        }
                        return true;
                    }
                });
                $.ajax(a);
            }
            return false;
        }
    };
}();