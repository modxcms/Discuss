[[+trail]]

[[!FormIt?
  &submitVar=`dis-post-reply`
  &hooks=`postHook.DiscussReplyMessage`
  &validate=`title:required,message:required:allowTags`
  &validationErrorMessage=`<p class="error">A form validation error occurred. Please check the values you have entered: [[+errors]]</p>`
]]

<div id="dis-reply-post-preview"></div>
<form action="[[~[[*id]]]]messages/reply?thread=[[!+fi.thread]]" method="post" class="dis-form" id="dis-reply-post-form" enctype="multipart/form-data">

    <h2>[[%discuss.post_reply? &namespace=`discuss` &topic=`post`]]</h2>
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
    
    
    
    <label for="dis-thread-message">[[%discuss.message]]:
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
        <input type="button" class="dis-action-btn dis-reply-post-preview" name="dis-post-preview" value="[[%discuss.preview]]" />
        <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]messages/view?thread=[[+thread]]';" />
    </div>
</form>

<br />
<hr />
<div class="dis-thread-posts">
    <h2>[[%discuss.thread_summary]]</h2>
[[+thread_posts]]
</div>

[[+discuss.error_panel]]



			</div><!-- Close Content From Wrapper -->
[[+bottom]]

				<div id="Panel">
					<div class="PanelBox">
					
						<div class="Box GuestBox">
						   <h4>Welcome back [[+modx.user.username]]</h4>
							<p>Some information. Test.</p>
						</div>
						
						<div class="Box BoxCategories">
							<h4><a href="/categories/all">Categories</a></h4>
							<ul class="PanelInfo PanelCategories">
								<li><span><strong><a href="/discussions">All Discussions</a></strong><span class="Count">[[+totalPosts]]</span></span></li>
								<li class="Heading">Partners</li>
								<li class="Depth2 active"><strong><a href="/categories/-important-news">Sample 1 Active</a></strong><span class="Count">0</span></li>
								<li class="Depth2"><strong><a href="/categories/security-notices">Sample 2</a></strong><span class="Count">0</span></li>
								<li class="Heading">General</li>
								<li class="Depth2 active"><strong><a href="/categories/-important-news">Sample 1 Active</a></strong><span class="Count">0</span></li>
								<li class="Depth2"><strong><a href="/categories/security-notices">Sample 2</a></strong><span class="Count">0</span></li>
								<li class="Heading">Evolution</li>
								<li class="Depth2 active"><strong><a href="/categories/-important-news">Sample 1 Active</a></strong><span class="Count">0</span></li>
								<li class="Depth2"><strong><a href="/categories/security-notices">Sample 2</a></strong><span class="Count">0</span></li>
								<li class="Heading">Revolution</li>
								<li class="Depth2 active"><strong><a href="/categories/-important-news">Sample 1 Active</a></strong><span class="Count">0</span></li>
								<li class="Depth2"><strong><a href="/categories/security-notices">Sample 2</a></strong><span class="Count">0</span></li>
								<li class="Heading">International</li>
								<li class="Depth2 active"><strong><a href="/categories/-important-news">Sample 1 Active</a></strong><span class="Count">0</span></li>
								<li class="Depth2"><strong><a href="/categories/security-notices">Sample 2</a></strong><span class="Count">0</span></li>
							</ul>
						</div>
					</div>
