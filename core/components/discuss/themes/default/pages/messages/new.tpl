[[+trail]]

[[!FormIt?
  &submitVar=`dis-message-new`
  &hooks=`postHook.DiscussNewMessage`
  &validate=`title:required,message:required:allowTags,participants_usernames:required`
]]

<div id="dis-message-preview"></div>
<br />
<form action="[[DiscussUrlMaker? &action=`messages/new`]]" method="post" class="dis-form" id="dis-message-new-form" enctype="multipart/form-data">

    <h2>[[%discuss.message_new? &namespace=`discuss` &topic=`post`]]</h2>

    <label for="dis-message-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label>
    <input type="text" name="title" id="dis-message-title" value="[[!+fi.title]]" />

    <label for="dis-message-participants">[[%discuss.participants]]:
        <span class="error">[[!+fi.error.participants_usernames]]</span>
        <span class="small">[[%discuss.participants_desc]]</span>
    </label>
    <input type="text" name="participants_usernames" id="dis-message-participants" value="[[!+fi.participants_usernames]]" />

    <div style="margin-left: 150px;">
        <br class="clear" />
        [[+buttons]]
        <br class="clear" />
    </div>

    <label for="dis-thread-message">[[%discuss.message]]:
        <span class="error">[[!+fi.error.message]]</span>
    </label>
    <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea>

    <label for="dis-message-attachment">[[%discuss.attachments]]:
        <span class="small dis-add-attachment"><a href="javascript:void(0);">[[%discuss.attachment_add]]</a>
        <br />([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span>
        <span class="error">[[+error.attachments]]</span>
    </label>
    <input type="file" name="attachment1" id="dis-message-attachment" />

    <div id="dis-attachments"></div>
    <br class="clear" />

    <div class="dis-form-buttons">
        <input type="submit" class="dis-action-btn" name="dis-message-new" value="[[%discuss.message_send]]" />
        <input type="button" class="dis-action-btn dis-message-preview" id="dis-message-preview-btn" value="[[%discuss.preview]]" />
        <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[DiscussUrlMaker? &action=`messages`]]';" />
    </div>
</form>