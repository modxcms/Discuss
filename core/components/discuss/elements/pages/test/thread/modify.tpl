<div id="board-wrapper">
	<ul class="breadcrumbs">
		[[+trail]]
	</ul>
	
	<hr class="clear spacer"/>
	
	<h3 class="maintitle">[[+subject]]</h3>
	<div id="dis-reply-post-preview">[[+preview]]</div>
	<!-- End #preview -->
	<form action="[[~[[*id]]? &post=`[[+id]]`]]" method="post" class="reply_form">
		<h3>[[%discuss.post_modify? &namespace=`discuss` &topic=`post`]]</h3>
		[[+discuss.error_panel:notempty=`<p class="error">[[+discuss.error_panel]]</p>`]]
		
		<input type="hidden" name="board" value="[[+board]]" />
		
		<label for="title">[[%discuss.title]]:
			<span class="error">[[+error.title]]</span>
		</label>
		<input type="text" name="title" id="title" value="[[+title]]" />
		<ul>						
			<li class="fields"> 						

				<label for="message">[[%discuss.message]]:
					<span class="error">[[+error.message]]</span>						
				</label>
				<textarea name="message" id="message" cols="80" rows="7">[[+message]]</textarea>						
			</li>
			<li>
				<label><input type="checkbox" name="locked" value="1" />[[%discuss.thread_lock? &namespace=`discuss` &topic=`web`]]</label>
				<label><input type="checkbox" name="sticky" value="1" />[[%discuss.thread_stick]]</label>
			</li>
			<li class="clearfix"> 	
				<div class="btns">
					<input type="submit" class="board-btns" value="[[%discuss.save_changes]]" />
					<input type="button" class="board-btns" value="[[%discuss.preview]]" />
					<input type="button" class="board-btns" value="[[%discuss.cancel]]" onclick="location.href='[[~[[++discuss.thread_resource]]? &thread=`[[+thread]]`]]';" />
				</div>
			</li> 
		</ul> 
	</form>
	<!-- End .form_reply -->
	
	[[+discuss.error_panel]]

	<h2 class="thread-summary">[[%discuss.thread_summary]]</h2>
	<ol class="board-posts">[[+thread_posts:default=`<p>[[%discuss.thread_no_posts]]</p>`]]</ol>		
</div>