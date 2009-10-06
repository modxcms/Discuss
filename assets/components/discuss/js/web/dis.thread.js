$(function() {
    $('.dis-poll-refresh').hide();
    DISThread.addEvents();
    //setTimeout('DISThread.pollPosts();',DIS.config.pollingInterval);
});

var DISThread = {
    postCount: 0
    ,addEvents: function() {
        $('.dis-post-title').click(DISThread.togglePost);
        $('.dis-post-author').click(DISThread.toggleAuthor);
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
                
                var diff = parseInt(r.message) - parseInt(DISThread.postCount);
                if (diff != 0) {
                    DISThread.displayRefreshMessage(diff);
                    DISThread.postCount = r.message;
                }
                setTimeout('DISThread.pollPosts();',DIS.config.pollingInterval);
            }
        });
        $.ajax(a);
    }
    
    ,displayRefreshMessage: function(d) {
        $('.dis-poll-refresh').html('There have been '+d+' new posts since you last reloaded. Please ' +
                '<a href="javascript:void(0);" onclick="DISThread.reloadThread();">click here</a> ' +
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
        //$(this).find('.dis-expand').show();
        $(this).find('.dis-author').slideToggle();
    }
    
    ,showReplyForm: function(id) {
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
                DISThread.hideReplyForm(id);
                DISThread.addEvents();
            }
        });
        $.ajax(a);
    }
    
    ,removePost: function(id,par,url) {
        var s = confirm('Are you sure you want to remove this post?');
        if (s) {
            var a = $.extend({},DIS.baseAjax,{
                url: DIS.config.connector
                ,data: {
                    action: 'web/post/remove'
                    ,post: id
                }
                ,success: function(r) {
                    if (r.success == false) { DIS._showError(r.message); return false; }
                    
                    var ol = $('#dis-board-post-'+id).parent('ol');
                    var lis = ol.children('li');
                    if (lis.length == 1) {
                        ol.fadeOut().remove();
                    } else {
                        $('#dis-board-post-'+id).fadeOut().remove();
                    }
                    var ct = parseInt($('.dis-author-post-count').html());
                    $('.dis-author-post-count').html((ct-1));
                    if (par == 0) {
                        location.href = url;
                    }
                }
            });
            $.ajax(a);
        }
    }
};