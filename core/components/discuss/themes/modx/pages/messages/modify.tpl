[[!FormIt?
  &submitVar=`dis-message-modify`
  &hooks=`postHook.DiscussModifyMessage`
  &validate=`title:required,message:required:allowTags,participants_usernames:required`
]]


    <div class="preview_toggle">
		<a href="#" class="dis-message-write selected" id="dis-message-write-btn">write</a>
        <a href="#" class="dis-modify-message-preview" id="dis-modify-message-preview-btn">preview</a>
		<div id="dis-modify-message-preview"></div>
    </div>
    

<form action="[[~[[*id]]]]messages/modify?post=[[!+fi.id]]" method="post" class="dis-form" id="dis-modify-message-form" enctype="multipart/form-data">

	<h1 class="Category">[[%discuss.message_modify? &namespace=`discuss` &topic=`post`]]</h1>

    <input type="hidden" name="post" value="[[!+fi.id]]" />
    <input type="hidden" name="thread" value="[[!+fi.thread]]" />

    <label for="dis-message-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label><br class="clearfix" />
    <input type="text" name="title" id="dis-message-title" value="[[!+fi.title]]" /><br class="clearfix" />

    <label for="dis-reply-participants">[[%discuss.participants]]:
        <span class="error">[[!+fi.error.participants_usernames]]</span>
        <span class="small">[[%discuss.participants_desc]]</span>
    </label><br class="clearfix" />
    <input type="text" name="participants_usernames" id="dis-reply-participants" value="[[!+fi.participants_usernames]]" /><br class="clearfix" />


    <div class="wysi-buttons">[[+buttons]]</div><br class="clearfix" />
    

    <label for="dis-thread-message">
        <span class="error">[[!+fi.error.message]]</span>
    </label><br class="clearfix" />
    <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea>
    <br class="clearfix" />


    <label for="dis-attachment">[[%discuss.attachments]]:
        <span class="small dis-add-attachment"><a href="javascript:void(0);">[[%discuss.attachment_add]]</a>
        <br />([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span>
        <span class="error">[[+error.attachments]]</span>
    </label>
        <br class="clearfix" />

    <input type="file" name="attachment[[+attachmentCurIdx]]" id="dis-attachment" />
    

    <div id="dis-attachments"></div>

    [[+attachments:notempty=`<div class="dis-existing-attachments">
        <ul>[[+attachments]]</ul>
    </div>`]]
    
    
<br class="clearfix" />
    <div class="dis-form-buttons">
        <input type="submit" class="dis-action-btn" name="dis-message-modify" value="[[%discuss.save_changes]]" />
        <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]messages/view?message=[[+thread]]#dis-post-[[+id]]';" />
    </div>
</form>



<div class="dis-thread-posts">
	<h1 class="Category">[[%discuss.thread_summary]]</h1>
	<div class="dis-thread-posts">

[[+thread_posts:default=`<p>[[%discuss.thread_no_posts]]</p>`]]
	</div>
</div>




			</div><!-- Close Content From Wrapper -->
[[+bottom]]

<div id="Panel">
				<hr class="line" />
    <div class="PanelBox">


		[[$actions-sidebar]]


    </div>
