

[[!FormIt?
  &submitVar=`dis-post-reply`
  &hooks=`postHook.DiscussReplyPost`
  &validate=`title:required,message:required:allowTags`
]]

<form action="[[~[[*id]]]]thread/reply?post=[[+id]]" method="post" class="dis-form" id="dis-reply-post-form" enctype="multipart/form-data">

	<ul class="DataList CategoryList CategoryListWithHeadings">
	
		<li class="Item CategoryHeading Depth1">
	    <div class="ItemContent Category">[[%discuss.post_reply? &namespace=`discuss` &topic=`post`]]</div>
	    </li>
	</ul>
	
    
    <input type="hidden" name="board" value="[[!+fi.board]]" />
    <input type="hidden" name="thread" value="[[!+fi.thread]]" />
    <input type="hidden" name="post" value="[[!+fi.post]]" />
    
    <label for="dis-reply-post-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label><br class="clearfix" />
    <input type="text" name="title" id="dis-reply-post-title" value="[[!+fi.title]]" /><br class="clearfix" />


    <div class="wysi-buttons">[[+buttons]]</div><br class="clearfix" />

    
    <label for="dis-thread-message">
        <span class="error">[[!+fi.error.message]]</span>
    </label><br class="clearfix" />
    <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea>
    <br class="clearfix" />

    [[+attachment_fields]]
    <br class="clearfix" />

    [[+locked_cb]]
    [[+sticky_cb]]
    <label class="dis-cb"><input type="checkbox" name="notify" value="1" />[[%discuss.notify_of_replies]]</label>
    
    <br class="clearfix" />
    
    <div class="dis-form-buttons">
        <input type="submit" name="dis-post-reply" value="[[%discuss.post_reply]]" />
        <input type="button" name="dis-post-preview" value="[[%discuss.preview]]" />
        <input type="button" value="[[%discuss.cancel]]" onclick="location.href='[[+url]]';" />
    </div>
</form>
<div id="dis-reply-post-preview"></div>

	<ul class="DataList CategoryList CategoryListWithHeadings">
	
		<li class="Item CategoryHeading Depth1">
	    <div class="ItemContent Category">[[%discuss.thread_summary]]</div>
	    </li>
	</ul>

<div class="dis-thread-posts">
[[+thread_posts]]
</div>

[[+discuss.error_panel]]
				</div><!-- Close Content From Wrapper -->

[[+bottom]]



				<div id="Panel">
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