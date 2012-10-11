$(document).ready(function() {
    var hiddenCategories = DisCookie.read('categories_hidden');
    hiddenCategories = (hiddenCategories) ? hiddenCategories.split('|') : [];
    $.each(hiddenCategories, function(index, value) {
        $('.category div.'+value).slideUp(200);
    });

    $('.dis-error').hide();
    $('.dis-cat-header').click(DISBoard.toggleCategory);
    $('.dis-action-login').click(function(event) {
        event.preventDefault();
        var duration = ($('body').scrollTop() > 200) ? 500 : 100;
        $('html, body').animate({scrollTop: 0}, duration, 'swing', function() {
            $('.masthead-login').fadeOut(200, function() {
                $(this).fadeIn(500, function() {
                    $('#login-username').focus();
                });
            });
        });
    });
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

var DISBoard = {
    toggleCategory: function() {
        var id = $(this).parent().attr('id');
        var cookieContents = DisCookie.read('categories_hidden');
        if (cookieContents) { cookieContents = cookieContents.split('|'); }
        else { cookieContents = []; }
        var indexOf = cookieContents.indexOf(id);

        var isVisible = $($('div.'+id)[0]).is(':visible');
        if (isVisible) {
            $('div.'+id).slideUp();
            if (indexOf == -1) {
                cookieContents.push(id);
            }
        } else {
            $('div.'+id).slideDown();
            if (indexOf != -1) {
                cookieContents.splice(indexOf, 1);
            }
        }
        cookieContents = cookieContents.join('|');
        DisCookie.create('categories_hidden',cookieContents);
        $(this).parent().toggleClass('dis-collapsed');
    }
};


jQuery.q = function(qs,options) {
    var q = (typeof qs === 'string'?qs:window.location.search)
        ,o = {'f':function(v){return unescape(v).replace(/\+/g,' ');}}
        , params = {};
    options = (typeof qs === 'object' && typeof options === 'undefined')?qs:options;
    o = jQuery.extend({}, o, options);
    jQuery.each(q.match(/^\??(.*)$/)[1].split('&'),function(i,p){
        p = p.split('=');
        p[1] = o.f(p[1]);
        params[p[0]] = params[p[0]]?((params[p[0]] instanceof Array)?(params[p[0]].push(p[1]),params[p[0]]):[params[p[0]],p[1]]):p[1];
    });
    return params;
};


function surroundText(text1, text2) {
    var textarea = $('#dis-thread-message')[0];
	// Can a text range be created?
	if (typeof(textarea.caretPos) != "undefined" && textarea.createTextRange) {
		var caretPos = textarea.caretPos, temp_length = caretPos.text.length;

		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text1 + caretPos.text + text2 + ' ' : text1 + caretPos.text + text2;

		if (temp_length == 0)
		{
			caretPos.moveStart("character", -text2.length);
			caretPos.moveEnd("character", -text2.length);
			caretPos.select();
		}
		else
			textarea.focus(caretPos);
	} else if (typeof(textarea.selectionStart) != "undefined") { // Mozilla text range wrap.
		var begin = textarea.value.substr(0, textarea.selectionStart);
		var selection = textarea.value.substr(textarea.selectionStart, textarea.selectionEnd - textarea.selectionStart);
		var end = textarea.value.substr(textarea.selectionEnd);
		var newCursorPos = textarea.selectionStart;
		var scrollPos = textarea.scrollTop;

		textarea.value = begin + text1 + selection + text2 + end;

		if (textarea.setSelectionRange) {
			if (selection.length == 0)
				textarea.setSelectionRange(newCursorPos + text1.length, newCursorPos + text1.length);
			else
				textarea.setSelectionRange(newCursorPos, newCursorPos + text1.length + selection.length + text2.length);
			textarea.focus();
		}
		textarea.scrollTop = scrollPos;
	} else {// Just put them on the end, then.
		textarea.value += text1 + text2;
		textarea.focus(textarea.value.length - 1);
	}
}

// Checks if the passed input's value is nothing.
function isEmptyText(theField) {
	// Copy the value so changes can be made..
	var theValue = theField.value;
	// Strip whitespace off the left side.
	while (theValue.length > 0 && (theValue.charAt(0) == ' ' || theValue.charAt(0) == '\t')) {
		theValue = theValue.substring(1, theValue.length);
    }
	// Strip whitespace off the right side.
	while (theValue.length > 0 && (theValue.charAt(theValue.length - 1) == ' ' || theValue.charAt(theValue.length - 1) == '\t')) {
		theValue = theValue.substring(0, theValue.length - 1);
    }
	return theValue == '';
}

// Remember the current position.
function storeCaret(text)
{
	// Only bother if it will be useful.
	if (typeof(text.createTextRange) != "undefined")
		text.caretPos = document.selection.createRange().duplicate();
}

// Replaces the currently selected text with the passed text.
function replaceText(text)
{
    var textarea = $('#dis-thread-message')[0];
	// Attempt to create a text range (IE).
	if (typeof(textarea.caretPos) != "undefined" && textarea.createTextRange)
	{
		var caretPos = textarea.caretPos;

		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
		caretPos.select();
	}
	// Mozilla text range replace.
	else if (typeof(textarea.selectionStart) != "undefined")
	{
		var begin = textarea.value.substr(0, textarea.selectionStart);
		var end = textarea.value.substr(textarea.selectionEnd);
		var scrollPos = textarea.scrollTop;

		textarea.value = begin + text + end;

		if (textarea.setSelectionRange)
		{
			textarea.focus();
			textarea.setSelectionRange(begin.length + text.length, begin.length + text.length);
		}
		textarea.scrollTop = scrollPos;
	}
	// Just put it on the end.
	else
	{
		textarea.value += text;
		textarea.focus(textarea.value.length - 1);
	}
}

var DisCookie = {
    create: function (name,value,days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
        }
        else var expires = "";
        document.cookie = name+"="+value+expires+"; path=/";
    },

    read: function(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    },

    erase: function(name) {
        this.create(name,"",-1);
    }
};
