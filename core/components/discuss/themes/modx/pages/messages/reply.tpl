

[[!FormIt?
  &submitVar=`dis-post-reply`
  &hooks=`postHook.DiscussReplyMessage`
  &validate=`title:required,message:required:allowTags`
  &validationErrorMessage=`<p class="error">A form validation error occurred. Please check the values you have entered: [[+errors]]</p>`
]]



    <div class="preview_toggle">
		<a href="#" class="dis-message-write selected" id="dis-message-write-btn">write</a>
        <a href="#" class="dis-reply-post-preview" id="dis-message-preview-btn">preview</a>
    	<div id="dis-reply-post-preview"></div>
    </div>

    
    
    

<form action="[[~[[*id]]]]messages/reply?thread=[[!+fi.thread]]" method="post" class="dis-form" id="dis-reply-post-form" enctype="multipart/form-data">

	<h1 class="Category">[[%discuss.post_reply? &namespace=`discuss` &topic=`post`]]</h1>
    <input type="hidden" name="thread" value="[[!+fi.thread]]" />
    <input type="hidden" name="post" value="[[!+fi.post]]" />

<div>[[+fi.validation_error_message]]</div>

    <label for="dis-reply-post-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label>
    <input type="text" name="title" id="dis-reply-post-title" value="[[!+fi.title]]" />

[[+is_author:notempty=`
    <label for="dis-reply-participants">[[%discuss.participants]]:
        <span class="error">[[!+fi.error.participants_usernames]]</span>
        <span class="small">[[%discuss.participants_desc]]</span>
    </label>
    <input type="text" name="participants_usernames" id="dis-reply-participants" value="[[!+fi.participants_usernames]]" />
`]]



    <div class="wysi-buttons">[[+buttons]]</div>
    
    
    
    <label for="dis-thread-message">
        <span class="error">[[!+fi.error.message]]</span>
    </label>
    <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea>
    <br class="clearfix" />
    
    <label for="dis-reply-post-attachment">[[%discuss.attachments]]:
        <span class="small dis-add-attachment"><a href="javascript:void(0);">[[%discuss.attachment_add]]</a>
        <br />([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span>
        <span class="error">[[+error.attachments]]</span>
    </label>
    <input type="file" name="attachment1" id="dis-reply-post-attachment" />    
    
    <div id="dis-attachments"></div>
    <br class="clearfix" />
    
    <div class="dis-form-buttons">
        <input type="submit" class="dis-action-btn" name="dis-post-reply" value="[[%discuss.message_send]]" />
        <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]messages/view?thread=[[+thread]]';" />
    </div>
</form>



<div class="dis-thread-posts">
	<h1 class="Category">[[%discuss.thread_summary]]</h1>
	
[[+thread_posts]]

</div>

[[+discuss.error_panel]]



			</div><!-- Close Content From Wrapper -->
[[+bottom]]

				<div id="Panel">
				<hr class="line" />
					<div class="PanelBox">
						
						<div class="Box GuestBox">
						   <h4>Don't Be That Guy</h4>
							<p>Be nice, respectful and patient. Inflamatory or inappropriate posts will get your post nuked and flood your life with bans and bad karma.</p>
						</div>
						
						<div class="Box GuestBox">
						   <h4>Other Support Options</h4>
							<p>To file a bug or make a feature request <a href="http://bugs.modx.com">visit our issue tracker</a>.</p>
						</div>
						
						<div class="Box GuestBox">
						   <h4>Want to Support MODX?</h4>
							<p>If you build sites for a living with MODX, why not <a href="http://modx.com/community/wall-of-fame/support-modx/">give back</a>?</p>
						</div>
						
					</div>
