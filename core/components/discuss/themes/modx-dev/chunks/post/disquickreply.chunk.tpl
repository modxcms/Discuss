<form action="[[~[[*id]]]][[+view]]" method="post" class="dis-form [[+locked:notempty=`locked`]]" id="dis-quick-reply-form" enctype="multipart/form-data">
    [[-<div class="preview_toggle">
		<a href="#" class="dis-message-write selected" id="dis-edit-btn">edit</a>
        <a href="#" class="dis-preview" id="dis-preview-btn">view</a>
    </div>
	<div id="dis-message-preview"></div>]]

    <input type="hidden" id="dis-quick-reply-board" name="board" value="[[+board]]" />
    <input type="hidden" id="dis-quick-reply-thread" name="thread" value="[[+id]]" />
    <input type="hidden" id="dis-quick-reply-post" name="post" value="[[+lastPost.id]]" />

    <input type="hidden" name="title" id="dis-quick-reply-title" value="Re: [[+title_value]]" />

    <div class="wysi-buttons">[[+reply_buttons]]</div>

    <textarea name="message" id="dis-thread-message">[[+message]]</textarea>

    [[+attachment_fields]]
    <br class="clearfix" />

    [[+locked_cb]]
    [[+sticky_cb]]

    <div class="dis-form-buttons">
       <input type="submit" name="dis-post-reply" value="Reply" /> <label class="dis-cb"><input type="checkbox" name="notify" value="1" [[+subscribed]] />Subscribe to replies via email</label>
    </div>
    <br class="clearfix" />
</form>
