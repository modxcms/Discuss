

[[!FormIt?
  &submitVar=`dis-post-reply`
  &hooks=`postHook.DiscussReplyMessage`
  &validate=`title:required,message:required:allowTags`
  &validationErrorMessage=`<p class="error">A form validation error occurred. Please check the values you have entered: [[+errors]]</p>`
]]




    
    
    

<form action="[[~[[*id]]]]messages/reply?thread=[[!+fi.thread]]" method="post" class="dis-form" id="dis-reply-post-form" enctype="multipart/form-data">

    <div class="preview_toggle">
		<a href="#" class="dis-message-write selected" id="dis-edit-btn">edit</a>
        <a href="#" class="dis-preview" id="dis-preview-btn">preview</a>
		<div id="dis-message-preview"></div>
    </div>

	<h1>[[%discuss.post_reply? &namespace=`discuss` &topic=`post`]]</h1>
    <input type="hidden" name="thread" value="[[!+fi.thread]]" />
    <input type="hidden" name="post" value="[[!+fi.post]]" />

<div>[[+fi.validation_error_message]]</div>

    <label for="dis-reply-post-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label><br />
    <input type="text" name="title" id="dis-reply-post-title" value="[[!+fi.title]]" /><br />

[[+is_author:notempty=`
    <label for="dis-reply-participants">[[%discuss.participants]]:
        <span class="error">[[!+fi.error.participants_usernames]]</span>
        <span class="small">[[%discuss.participants_desc]]</span>
    </label><br />
    <input type="text" name="participants_usernames" id="dis-reply-participants" value="[[!+fi.participants_usernames]]" /><br />
`]]



    <div class="wysi-buttons">[[+buttons]]</div>
    
    
    
    <label for="dis-thread-message">Reply:
        <span class="error">[[!+fi.error.message]]</span>
    </label><br />
    <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea>
    <br class="clearfix" />
    
    <label for="dis-reply-post-attachment">
        <span class="small dis-add-attachment"><a href="javascript:void(0);">[[%discuss.attachment_add]]</a> ([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span>
        <span class="error">[[+error.attachments]]</span>
    </label><br />
    <input type="file" name="attachment1" id="dis-reply-post-attachment" />    
    
    <div id="dis-attachments"></div>
    <br class="clearfix" />
    
    <div class="dis-form-buttons">
        <input type="submit" class="dis-action-btn" name="dis-post-reply" value="[[%discuss.message_send]]" />
        <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]messages/view?thread=[[+thread]]';" />
    </div>
</form>



<div class="dis-thread-posts">
		<ul class="dis-list">
			<li><h1>[[%discuss.thread_summary]]</h1></li>
			[[+thread_posts]]
		</ul>
</div>

[[+discuss.error_panel]]



			</div><!-- Close Content From Wrapper -->
[[+bottom]]

<aside>
				<hr class="line" />
    <div class="PanelBox">


		[[!$post-sidebar?disection=`new-message`]]


</aside>
