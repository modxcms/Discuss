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