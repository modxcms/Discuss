[[!FormIt?
  &submitVar=`dis-post-reply`
  &hooks=`postHook.DiscussReplyPost`
  &validate=`title:required,message:required:allowTags`
]]


	    <div class="preview_toggle">
			<a href="#" class="dis-message-write selected" id="dis-message-write-btn">write</a>
        	<a href="#" class="dis-preview" id="dis-post-preview">preview</a>
			<div id="dis-reply-post-preview"></div>
	    </div>
	    
<form action="[[~[[*id]]]]thread/reply?post=[[+id]]" method="post" class="dis-form" id="dis-reply-post-form" enctype="multipart/form-data">

	<h1 class="Category">[[!+fi.title]]</h1>


    
    <input type="hidden" name="board" value="[[!+fi.board]]" />
    <input type="hidden" name="thread" value="[[!+fi.thread]]" />
    <input type="hidden" name="post" value="[[!+fi.post]]" />
    <input type="hidden" name="title" id="dis-reply-post-title" value="[[!+fi.title]]" /><br class="clearfix" />
	
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
        <input type="button" value="[[%discuss.cancel]]" onclick="location.href='[[+url]]';" />
    </div>
</form>



	<h1 class="Category">[[%discuss.thread_summary]]</h1>

<div class="dis-thread-posts">
[[+thread_posts]]
</div>

[[+discuss.error_panel]]
				</div><!-- Close Content From Wrapper -->

[[+bottom]]


<div id="Panel">
				<hr class="line" />
    <div class="PanelBox">


		[[$actions-sidebar]]


    </div>