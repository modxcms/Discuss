[[!FormIt?
  &submitVar=`dis-message-new`
  &hooks=`postHook.DiscussNewMessage`
  &validate=`title:required,message:required:allowTags,participants_usernames:required`
]]
<div class="f1-f9 twelve-form">
    <h1>[[%discuss.message_new? &namespace=`discuss` &topic=`post`]]</h1>
    <form action="[[~[[*id]]]]messages/new" method="post" class="dis-form" id="dis-message-new-form" enctype="multipart/form-data">
        <label for="dis-message-title">[[%discuss.title]]:
            <span class="error">[[!+fi.error.title]]</span>
        </label>
        <br class="clearfix" />
        <input type="text" name="title" id="dis-message-title" value="[[!+fi.title]]" />
        <br class="clearfix" />

        <label for="dis-message-participants">[[%discuss.participants]]:
            <span class="error">[[!+fi.error.participants_usernames]]</span>
            <span class="small">[[%discuss.participants_desc]]</span>
        </label><br class="clearfix" />
        <input type="text" name="participants_usernames" id="dis-message-participants" value="[[!+fi.participants_usernames]]" /><br class="clearfix" />


        <label for="dis-thread-message">
            <span class="error">[[!+fi.error.message]]</span>
        </label><br class="clearfix" />
        <div class="wysi-buttons">[[+buttons]]</div>
        <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea><br class="clearfix" />

        <div class="h-group">
            <div class="l-left">
                <label for="dis-message-attachment">[[%discuss.attachments]]:
                    <span class="small"><a href="javascript:void(0);"class="dis-add-attachment">[[%discuss.attachment_add]]</a>
                    <br /> ([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span>
                    <span class="error">[[+error.attachments]]</span>
                </label>
                <input type="file" name="attachment1" id="dis-message-attachment" />

                <br class="clearfix" />

                <div id="dis-attachments"></div>
                [[+attachments:notempty=`
                    <div class="dis-existing-attachments">
                        <ul class="dis-attachments">[[+attachments]]</ul>
                    </div>
                `]]
            </div>

            <div class="dis-form-buttons l-right">
                <input type="button" class="cancel" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]messages';" />
                <input type="submit" class="" name="dis-message-new" value="[[%discuss.message_send]]" />
            </div>
        </div>
    </form>
</div>

[[+bottom]]

[[+sidebar]]

