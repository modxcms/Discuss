

[[!FormIt?
  &submitVar=`dis-post-new`
  &hooks=`postHook.DiscussNewThread`
  &validate=`title:required,message:required:allowTags`
]]


    <div class="preview_toggle">
		<a href="#" class="dis-message-write selected" id="dis-message-write-btn">write</a>
        <a href="#" class="dis-new-thread-preview" id="dis-new-thread-preview-btn">preview</a>
    	<div id="dis-new-thread-preview"></div>
    </div>
    
<form action="[[~[[*id]]]]thread/new?board=[[+id]]" method="post" class="dis-form" id="dis-new-thread-form" enctype="multipart/form-data">
	<h1 class="Category">[[%discuss.start_new_thread? &namespace=`discuss` &topic=`post`]]</h1>
	

    
  
    
    
    
    
    
    
    
    <input type="hidden" name="board" value="[[+id]]" />
    
    <label for="dis-new-thread-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label><br class="clearfix" />
    <input type="text" name="title" id="dis-new-thread-title" value="[[!+fi.title]]" /><br class="clearfix" />

    <label for="dis-new-thread-type">[[%discuss.thread_type]]:</label><br class="clearfix" />
    <select name="class_key" id="dis-new-thread-type">
        <option value="disThreadDiscussion">[[%discuss.discussion]]</option>
        <option value="disThreadQuestion">[[%discuss.question_and_answer]]</option>
    </select><br class="clearfix" />

    <div class="wysi-buttons">[[+buttons]]</div><br class="clearfix" />


    <label for="dis-thread-message">
        <span class="error">[[!+fi.error.message]]</span>
    </label><br class="clearfix" />
    <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea><br class="clearfix" />

    [[+attachment_fields]]

    <br class="clearfix" />



    <div class="dis-form-buttons">
    [[+locked_cb]]
    [[+sticky_cb]]
    <label class="dis-cb"><input type="checkbox" name="notify" value="1" [[!+fi.notify:FormItIsChecked=`1`]] />[[%discuss.notify_of_replies]]</label>
        <input type="submit" name="dis-post-new" value="[[%discuss.thread_post_new]]" />
        <input type="button" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]board/?board=[[+id]]';" />
    </div>
</form>
<br class="clearfix" />


[[+discuss.error_panel]]

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
