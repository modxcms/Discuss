[[!FormIt?
  &submitVar=`dis-message-modify`
  &hooks=`postHook.DiscussModifyMessage`
  &validate=`title:required,message:required:allowTags,participants_usernames:required`
]]

<div class="f1-f9 twelve-form">
    <h1>[[%discuss.message_modify? &namespace=`discuss` &topic=`post`]]</h1>
    <form action="[[~[[*id]]]]messages/modify?post=[[!+fi.id]]" method="post" class="dis-form" id="dis-modify-message-form" enctype="multipart/form-data">
        <input type="hidden" name="post" value="[[!+fi.id]]" />
        <input type="hidden" name="thread" value="[[!+fi.thread]]" />

        <label for="dis-message-title">[[%discuss.title]]:
            <span class="error">[[!+fi.error.title]]</span>
        </label>
        <br class="clearfix" />
        <input type="text" name="title" id="dis-message-title" value="[[!+fi.title]]" />
        <br class="clearfix" />

        <label for="dis-reply-participants">[[%discuss.participants]]: [[!+fi.participants_usernames_linked]]
            <span class="error">[[!+fi.error.participants_usernames]]</span>
            <span class="small">[[%discuss.participants_desc]]</span>
        </label><br class="clearfix" />
        <input type="text" name="participants_usernames" id="dis-reply-participants" value="[[!+fi.participants_usernames]]" />

        <br class="clearfix" />

        <label for="dis-thread-message">
            <span class="error">[[!+fi.error.message]]</span>
        </label><br class="clearfix" />
        <div class="wysi-buttons">[[+buttons]]</div>
        <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea><br class="clearfix" />



        <div class="h-group">
            <div class="l-left">
                <label for="dis-attachment">[[%discuss.attachments]]:
                    <span class="small"><a href="javascript:void(0);" class="dis-add-attachment">[[%discuss.attachment_add]]</a>
                    <br />([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span>
                    <span class="error">[[+error.attachments]]</span>
                </label>
                <input type="file" name="attachment[[+attachmentCurIdx]]" id="dis-attachment" />

                <br class="clearfix" />

                <div id="dis-attachments"></div>
                [[+attachments:notempty=`
                    <div class="dis-existing-attachments">
                        <ul>[[+attachments]]</ul>
                    </div>
                `]]
            </div>

            <div class="dis-form-buttons l-right">
                <input type="button" class="cancel" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]messages/view?message=[[+thread]]#dis-post-[[+id]]';" />
                <input type="submit" class="" name="dis-message-modify" value="[[%discuss.save_changes]]" />
            </div>
        </div>
    </form>
</div>

[[+bottom]]

[[+sidebar]]


<div class="f1-f9 dis-thread-posts">
    <ul class="dis-list">
        <li><h1>[[%discuss.thread_summary]]</h1></li>
        [[+thread_posts:default=`<li>[[%discuss.thread_no_posts]]</li>`]]
    </ul>
</div>
