
$(document).keyup(keyTrigger);
function keyTrigger(kbkey){
    var lkey = 37;
    var rkey = 39;
    var replyKey = 82;
    if (kbkey.target.localName == 'body' || kbkey.target.localName == 'html') {
        var code = kbkey.which;
        if (code == lkey)
            prevP();
        else if (code == rkey)
            nextP();
        else if (code == replyKey)
            replyL();
        }
    }
var newPage;
function prevP(){
    var href = $('ul.paginate li:first-child a').attr('href');
    if (href && href != newPage) {
        document.location = href;
        newPage = href;
    }
}
function nextP(){
    var href = $('ul.paginate li:last-child a').attr('href');
    if (href && href != newPage) {
        document.location = href;
        newPage = href;
    }
}
function replyL(){
    var quickReply;
    quickReply = $('#dis-quick-reply-form').offset().top;
    $('html,body').animate({scrollTop:quickReply}, 200, function(){
        $('#dis-thread-message').focus();
    });
}