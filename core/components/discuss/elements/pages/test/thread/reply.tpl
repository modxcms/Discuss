<div id="board-wrapper">
	<ul class="breadcrumbs">
		[[+trail]]
	</ul>
	
	<hr class="clear spacer"/>
	
	<h3 class="maintitle">[[+subject]]</h3>
	<div id="dis-reply-post-preview">[[+preview]]</div>
	<!-- End #preview -->
	<form action="[[~[[*id]]? &post=`[[+id]]`]]" method="post" class="reply_form"  enctype="multipart/form-data">
		<h3>[[%discuss.post_reply? &namespace=`discuss` &topic=`post`]]</h3>
		[[+discuss.error_panel:notempty=`<p class="error">[[+discuss.error_panel]]</p>`]]
		
		<input type="hidden" name="board" value="[[+board]]" />
		<input type="hidden" name="post" value="[[+id]]" />
		
		<label for="title">[[%discuss.title]]:
			<span class="error">[[+error.title]]</span>
		</label>
		<input type="text" name="title" id="title" value="[[+post.title]]" />
		<ul>						
			<li class="fields"> 						

				<label for="message">[[%discuss.message]]:
					<span class="error">[[+error.message]]</span>						
				</label>
				<textarea name="message" id="message" cols="80" rows="7">[[+message]]</textarea>						
			</li>
			<li>
				<label for="attachments">[[%discuss.attachments]]:
					<span class="small dis-reply-post-add-attachment"><a href="[[~[[*id]]? &post=`[[+id]]`]]">[[%discuss.attachment_add]]</a>
					<br />([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span>
					<span class="error">[[+error.attachments]]</span>
				</label>
				<input type="file" name="attachment1" id="attachments" />
			</li>					
			<li class="clearfix"> 
				<div id="dis-attachments"></div>
			</li>	
			<li>
				<label><input type="checkbox" name="locked" value="1" />[[%discuss.thread_lock? &namespace=`discuss` &topic=`web`]]</label>
				<label><input type="checkbox" name="sticky" value="1" />[[%discuss.thread_stick]]</label>
				<label><input type="checkbox" name="notify" value="1" />[[%discuss.notify_of_replies]]</label>
			</li>
			<li class="clearfix"> 	
				<div class="btns">
					<input type="submit" class="board-btns" value="[[%discuss.post_reply]]" />
					<input type="button" class="board-btns" value="[[%discuss.preview]]" />
					<input type="button" class="board-btns" value="[[%discuss.cancel]]" onclick="location.href='[[~[[++discuss.thread_resource]]? &thread=`[[+thread]]`]]';" />
				</div>
			</li> 
		</ul> 
	</form>
	<!-- End #form reply -->

	<h2 class="thread-summary">[[%discuss.thread_summary]]</h2>
	<ol class="board-posts">[[+thread_posts]]</ol>		
</div>