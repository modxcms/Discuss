<form action="[[~[[*id]]]][[+view]]" method="post" class="dis-form dis-thread-form [[+locked:notempty=`locked`]]" id="dis-quick-reply-form" enctype="multipart/form-data">
    <input type="hidden" id="dis-quick-reply-board"  name="board"  value="[[+board]]" />
    <input type="hidden" id="dis-quick-reply-thread" name="thread" value="[[+id]]" />
    <input type="hidden" id="dis-quick-reply-post"   name="post"   value="[[+lastPost.id]]" />
    <input type="hidden" id="dis-quick-reply-title"  name="title"  value="Re: [[+title_value]]" />

    <div class="wysi-buttons">[[+reply_buttons]]</div>
    <div class="h-group">
        <textarea name="message" id="dis-thread-message">[[+message]]</textarea>
    </div>
    <span class="error">[[!+fi.error.message]]</span>
    <div class="h-group below-wysi">
        <div class="l-left">[[+attachment_fields]]</div>
        <div class="dis-form-buttons l-right">
            <input class="a-reply" type="submit" name="dis-post-reply" value="Reply" />
            <div class="group">
                [[+locked_cb]]
                [[+sticky_cb]]
                <label class="dis-cb">
                    <input type="checkbox" name="notify" value="1" [[+subscribed]] />[[%discuss.subscribe_by_email]]
                </label>
            </div>
        </div>
    </div>
    [[+discuss.error_panel]]
</form>





[[- <!--<div class="h-group below-wysi">
        <div class="l-left">
            <label for="dis-attachment">[[%discuss.attachments]]:
                <span class="error">[[+error.attachments]]</span>
            </label>
            <input type="file" class="dis-attachment-input" name="attachment[[+attachmentCurIdx]]" id="dis-attachment" />

            <div id="dis-attachments"></div>
            [[+attachments:notempty=`
                <div class="dis-existing-attachments">
                    <ul class="dis-attachments">[[+attachments]]</ul>
                </div>
            `]]

            <a href="javascript:void(0);" class="dis-add-attachment">[[%discuss.attachment_add]] <span>([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span></a>
        </div>

        <div class="dis-form-buttons l-right">
            <a class="cancel" onclick="location.href='[[+url]]';" />[[%discuss.cancel]]</a>
            <input type="submit" name="dis-post-reply" value="[[%discuss.post_[[+action]]]]" />
            <div class="group">
                [[+locked_cb]]
                [[+sticky_cb]]
                <label class="dis-cb">
                    <input type="checkbox" name="notify" value="1" checked="checked" />[[%discuss.subscribe_by_email]]
                </label>
            </div>
        </div>
    </div> -->]]