$(function() {
    $('.dis-error').hide();
});
var DIS = {
    config: {}
    ,baseAjax: {
        type: 'POST'
        ,dataType: 'json'
        ,failure: function(r) {
            DIS._showError(r);
        }
    }
    
    ,_showError: function(msg,success) {
        var d = $('.dis-error');
        if (success) {
            d.removeClass('error');
            d.addClass('success');
        } else {
            d.removeClass('success');
            d.addClass('error');
        }
        $('.dis-error .dis-content').html(msg);
        d.hide().fadeIn(300);
        setTimeout('DIS._closeError();',5000);
    }
    ,_closeError: function() {
        $('.dis-error').fadeOut(300);
    }
};

var DISBar = {
    state: 'open'
    ,toggle: function() {
        $('.dis-bar-content').slideToggle();
        if (DISBar.state == 'open') {
            DISBar.state = 'closed';
            $('.dis-bar-toggle').text('Open Bar');
        } else {
            DISBar.state = 'open';
            $('.dis-bar-toggle').text('Close Bar');
        }
    }
    
};


jQuery.q = function(qs,options) {
    var q = (typeof qs === 'string'?qs:window.location.search), o = {'f':function(v){return unescape(v).replace(/\+/g,' ');}}, options = (typeof qs === 'object' && typeof options === 'undefined')?qs:options, o = jQuery.extend({}, o, options), params = {};
    jQuery.each(q.match(/^\??(.*)$/)[1].split('&'),function(i,p){
        p = p.split('=');
        p[1] = o.f(p[1]);
        params[p[0]] = params[p[0]]?((params[p[0]] instanceof Array)?(params[p[0]].push(p[1]),params[p[0]]):[params[p[0]],p[1]]):p[1];
    });
    return params;
};