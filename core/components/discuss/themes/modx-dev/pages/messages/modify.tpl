[[!FormIt?
  &submitVar=`dis-message-modify`
  &hooks=`postHook.DiscussModifyMessage`
  &validate=`title:required,message:required:allowTags,participants_usernames:required`
]]



    

<form action="[[~[[*id]]]]messages/modify?post=[[!+fi.id]]" method="post" class="dis-form" id="dis-modify-message-form" enctype="multipart/form-data">


    <div class="preview_toggle">
		<a href="#" class="dis-message-write selected" id="dis-edit-btn">edit</a>
        <a href="#" class="dis-preview" id="dis-preview-btn">view</a>
    </div>
	<div id="dis-message-preview"></div>
    
    
    
    
	<h1>[[%discuss.message_modify? &namespace=`discuss` &topic=`post`]]</h1>

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
        <span class="small"><a href="javascript:void(0);" class="dis-add-attachment">[[%discuss.attachment_add]]</a>
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
		<ul class="dis-list">
			<li><h1>[[%discuss.thread_summary]]</h1></li>
			[[+thread_posts:default=`<p>[[%discuss.thread_no_posts]]</p>`]]
</div>




			</div><!-- Close Content From Wrapper -->
[[+bottom]]

[[+sidebar]]
