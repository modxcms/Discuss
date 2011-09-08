
[[!FormIt?
  &submitVar=`dis-message-new`
  &hooks=`postHook.DiscussNewMessage`
  &validate=`title:required,message:required:allowTags,participants_usernames:required`
]]




<form action="[[~[[*id]]]]messages/new" method="post" class="dis-form" id="dis-message-new-form" enctype="multipart/form-data">
    
    <div class="preview_toggle">
		<a href="#" class="dis-message-write selected" id="dis-edit-btn">edit</a>
        <a href="#" class="dis-preview" id="dis-preview-btn">preview</a>
		<div id="dis-message-preview"></div>
    </div>
    
    
    
	<h1>[[%discuss.message_new? &namespace=`discuss` &topic=`post`]]</h1>



    <label for="dis-message-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label><br class="clearfix" />
    <input type="text" name="title" id="dis-message-title" value="[[!+fi.title]]" /><br class="clearfix" />

    <label for="dis-message-participants">[[%discuss.participants]]:
        <span class="error">[[!+fi.error.participants_usernames]]</span>
        <span class="small">[[%discuss.participants_desc]]</span>
    </label><br class="clearfix" />
    <input type="text" name="participants_usernames" id="dis-message-participants" value="[[!+fi.participants_usernames]]" /><br class="clearfix" />


    <div class="wysi-buttons">[[+buttons]]</div>


    <label for="dis-thread-message">
        <span class="error">[[!+fi.error.message]]</span>
    </label><br class="clearfix" />
    <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea><br class="clearfix" />

    <label for="dis-message-attachment">
        <span class="small dis-add-attachment"><a href="javascript:void(0);">[[%discuss.attachment_add]]</a>([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span>
        <span class="error">[[+error.attachments]]</span>
    </label><br class="clearfix" />
    <input type="file" name="attachment1" id="dis-message-attachment" />
<br class="clearfix" />
    <div id="dis-attachments"></div>
    <br class="clearfix" />

    <div class="dis-form-buttons">
        <input type="submit" class="dis-action-btn" name="dis-message-new" value="[[%discuss.message_send]]" />
        <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]messages';" />
    </div>
</form>


			</div><!-- Close Content From Wrapper -->
[[+bottom]]

<div id="Panel">
				<hr class="line" />
    <div class="PanelBox">


		[[!$post-sidebar?disection=`new-message`]]


    </div>
