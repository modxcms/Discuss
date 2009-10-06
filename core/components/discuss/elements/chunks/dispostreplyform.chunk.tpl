<form action="#" method="post" class="dis-reply-form" id="dis-reply-form-[[+id]]" onsubmit="DISThread.postReply([[+id]]); return false;">
    <input type="hidden" name="post" value="[[+id]]" />
    
    <label class="dis-reply-title">Title:
    <input type="text" name="title" value="Re: [[+title]]" />
    </label>
    <br />
    <textarea name="message" cols="60" rows="4"></textarea>
    <br />

    <input type="submit" class="dis-reply-submit" value="Post" />
    <input type="button" class="dis-reply-cancel" value="Cancel" onclick="DISThread.hideReplyForm([[+id]]);" />
</form>