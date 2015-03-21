<form action="[[~[[*id]]]][[+view]]" method="post" class="dis-form dis-thread-form [[+locked:notempty=`locked`]]" id="dis-quick-reply-form" enctype="multipart/form-data">
    <input type="hidden" id="dis-quick-reply-board"  name="board"  value="[[+board]]" />
    <input type="hidden" id="dis-quick-reply-thread" name="thread" value="[[+id]]" />
    <input type="hidden" id="dis-quick-reply-post"   name="post"   value="[[+lastPost.id]]" />
    <input type="hidden" id="dis-quick-reply-title"  name="title"  value="Re: [[+title_value]]" />

    <div class="wysi-buttons">[[+reply_buttons]]</div>
    <div class="h-group">
        <textarea name="message" id="dis-thread-message" tabindex="10">[[+message]]</textarea>
    </div>
    <span class="error">[[!+fi.error.message]]</span>
    <div class="h-group below-wysi">
        <div class="l-left">[[+attachment_fields]]</div>
        <div class="dis-form-buttons l-right">
            <input class="a-reply" type="submit" name="dis-post-reply" value="Reply" tabindex="40"/>
            <div class="group">
                [[+locked_cb]]
                [[+sticky_cb]]
                <label class="dis-cb">
                    <input type="checkbox" name="notify" value="" [[+subscribed]]  tabindex="38"/>[[%discuss.subscribe_by_email]]
                </label>
            </div>
        </div>
    </div>
    [[+discuss.error_panel]]
</form>