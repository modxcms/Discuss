<form action="[[~[[*id]]]][[+view]]" method="post" class="dis-form dis-thread-form [[+locked:notempty=`locked`]]" id="dis-quick-reply-form" enctype="multipart/form-data">
    <input type="hidden" id="dis-quick-reply-board"  name="board"  value="[[+board]]" />
    <input type="hidden" id="dis-quick-reply-thread" name="thread" value="[[+id]]" />
    <input type="hidden" id="dis-quick-reply-post"   name="post"   value="[[+lastPost.id]]" />
    <input type="hidden" id="dis-quick-reply-title"  name="title"  value="Re: [[+title_value]]" />

    <div class="wysi-buttons">[[+reply_buttons]]</div>
    <textarea name="message" id="dis-thread-message">[[+message]]</textarea>
    <span class="error">[[!+fi.error.message]]</span>

    <div class="l-left">[[+attachment_fields]]</div>
    <div class="dis-form-buttons l-right">
        [[+locked_cb]]
        [[+sticky_cb]]
        <label class="dis-cb"><input type="checkbox" name="notify" value="1" [[+subscribed]] />[[%discuss.subscribe_by_email]]</label>
        <input class="a-reply" type="submit" name="dis-post-reply" value="Reply" />
    </div>
    [[+discuss.error_panel]]
</form>
