<form action="#" method="post" class="dis-reply-form" id="dis-reply-form-[[+id]]" onsubmit="DISThread.postReply([[+id]]); return false;">
    <input type="hidden" name="post" value="[[+id]]" />
    
    <label class="dis-reply-title">[[%discuss.title]]:
    <input type="text" name="title" value="Re: [[+title]]" />
    </label>
    <br />
    <textarea name="message" cols="60" rows="4"></textarea>
    <br />

    <input type="submit" class="dis-reply-submit" value="[[%discuss.post_reply]]" />
    <input type="button" class="dis-reply-cancel" value="[[%discuss.cancel]]" onclick="DISThread.hideReplyForm([[+id]]);" />
</form>