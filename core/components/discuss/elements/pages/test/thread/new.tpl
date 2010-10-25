<div id="board-wrapper">
	<ul class="breadcrumbs">
		[[+trail]]
	</ul>

	<hr class="clear spacer"/>
	
	<div id="dis-reply-post-preview">[[+preview]]</div>
	<!-- End #preview -->
	<form action="[[~[[*id]]? &board=`[[+board]]`]]" method="post" class="reply_form">
		<h3>[[%discuss.start_new_thread? &namespace=`discuss` &topic=`post`]]</h3>
		[[+discuss.error_panel:notempty=`<p class="error">[[+discuss.error_panel]]</p>`]]
		
		<input type="hidden" name="board" value="[[+board]]" />
		
		<label for="title">[[%discuss.title]]:
			<span class="error">[[+error.title]]</span>
		</label>
		<input type="text" name="title" id="title" value="" />
		<ul>						
			<li class="fields"> 						

				<label for="message">[[%discuss.message]]:
					[[+error.message:notempty=`<span class="error">[[+error.message]]</span>`]]						
				</label>
				<textarea name="message" id="message" cols="80" rows="7"></textarea>						
			</li>
			<li>
				<label for="attachments">[[%discuss.attachments]]:
					<span class="small dis-reply-post-add-attachment"><a href="[[~[[*id]]? &board=`[[+board]]`]]">[[%discuss.attachment_add]]</a>
					([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span>
					[[+error.attachments:notempty=`<span class="error">[[+error.attachments]]</span>`]]
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
					<input type="submit" class="board-btns" value="[[%discuss.thread_post_new]]" />
					<input type="button" class="board-btns" value="[[%discuss.preview]]" />
					<input type="button" class="board-btns" value="[[%discuss.cancel]]" onclick="location.href='[[~[[++discuss.board_resource]]? &board=`[[+board]]`]]';" />
				</div>
			</li> 
		</ul> 
	</form>
	<!-- End .form_reply -->
	[[+discuss.error_panel]]	
</div>