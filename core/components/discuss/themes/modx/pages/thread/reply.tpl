[[!FormIt?
  &submitVar=`dis-post-reply`
  &hooks=`postHook.DiscussReplyPost`
  &validate=`title:required,message:required:allowTags`
]]



	    
<form action="[[~[[*id]]]]thread/reply?post=[[+id]]" method="post" class="dis-form" id="dis-reply-post-form" enctype="multipart/form-data">


    <div class="preview_toggle">
		<a href="#" class="dis-message-write selected" id="dis-edit-btn">edit</a>
        <a href="#" class="dis-preview" id="dis-preview-btn">view</a>
    </div>
	<div id="dis-message-preview"></div>
	    
	    
	<h1>[[!+fi.title]]</h1>


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



	

<div class="dis-thread-posts">
<ul class="dis-list">
	<li><h1>[[%discuss.thread_summary]]</h1></li>
[[+thread_posts]]
</ul>
</div>

[[+discuss.error_panel]]
				</div><!-- Close Content From Wrapper -->

[[+bottom]]


<aside>
				<hr class="line" />
    <div class="PanelBox">

	[[!$post-sidebar?disection=`new-message`]]

						
</aside>