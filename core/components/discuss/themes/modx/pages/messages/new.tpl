
[[!FormIt?
  &submitVar=`dis-message-new`
  &hooks=`postHook.DiscussNewMessage`
  &validate=`title:required,message:required:allowTags,participants_usernames:required`
]]


    <div class="preview_toggle">
		<a href="#" class="dis-message-write selected" id="dis-message-write-btn">write</a>
        <a href="#" class="dis-message-preview" id="dis-message-preview-btn">preview</a>
    	<div id="dis-message-preview"></div>
    </div>

<form action="[[~[[*id]]]]messages/new" method="post" class="dis-form" id="dis-message-new-form" enctype="multipart/form-data">

	<h1 class="Category">[[%discuss.message_new? &namespace=`discuss` &topic=`post`]]</h1>



    <label for="dis-message-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label>
    <input type="text" name="title" id="dis-message-title" value="[[!+fi.title]]" />

    <label for="dis-message-participants">[[%discuss.participants]]:
        <span class="error">[[!+fi.error.participants_usernames]]</span>
        <span class="small">[[%discuss.participants_desc]]</span>
    </label>
    <input type="text" name="participants_usernames" id="dis-message-participants" value="[[!+fi.participants_usernames]]" />


    <div class="wysi-buttons">[[+buttons]]</div>


    <label for="dis-thread-message">
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
						
						<div class="Box GuestBox">
						   <h4>Don't Be That Guy</h4>
							<p>Be nice, respectful and patient. Inflamatory or inappropriate posts will get your post nuked and flood your life with bans and bad karma.</p>
						</div>
						
						<div class="Box GuestBox">
						   <h4>Help Us Help You</h4>
							<p>Use a title that gives insight into your post and limit your posts to 1. Remember, this is an open source project and folks aren't paid to help you here. If you're experiencing problems, please supply adequate technical details.</p>
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
