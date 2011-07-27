

[[!FormIt?
  &submitVar=`dis-post-new`
  &hooks=`postHook.DiscussNewThread`
  &validate=`title:required,message:required:allowTags`
]]


<form action="[[~[[*id]]]]thread/new?board=[[+id]]" method="post" class="dis-form" id="dis-new-thread-form" enctype="multipart/form-data">
	<ul class="DataList CategoryList CategoryListWithHeadings">
	
		<li class="Item CategoryHeading Depth1">
	    <div class="ItemContent Category">[[%discuss.start_new_thread? &namespace=`discuss` &topic=`post`]]</div>
	    </li>
	</ul>
    
    <input type="hidden" name="board" value="[[+id]]" />
    
    <label for="dis-new-thread-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label>
    <input type="text" name="title" id="dis-new-thread-title" value="[[!+fi.title]]" />

    <label for="dis-new-thread-type">[[%discuss.thread_type]]:</label>
    <select name="class_key" id="dis-new-thread-type">
        <option value="disThreadDiscussion">[[%discuss.discussion]]</option>
        <option value="disThreadQuestion">[[%discuss.question_and_answer]]</option>
    </select>

    <div class="wysi-buttons">[[+buttons]]</div>


    <label for="dis-thread-message">
        <span class="error">[[!+fi.error.message]]</span>
    </label>
    <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea>

    [[+attachment_fields]]

    <br class="clearfix" />


    [[+locked_cb]]
    [[+sticky_cb]]

    <label class="dis-cb"><input type="checkbox" name="notify" value="1" [[!+fi.notify:FormItIsChecked=`1`]] />[[%discuss.notify_of_replies]]</label><br class="clear" />

    <div class="dis-form-buttons">
        <input type="submit" name="dis-post-new" value="[[%discuss.thread_post_new]]" />
        <input type="button" class="dis-new-thread-preview" id="dis-new-thread-preview-btn" value="[[%discuss.preview]]" />
        <input type="button" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]board/?board=[[+id]]';" />
    </div>
</form>

<div id="dis-new-thread-preview"></div>

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
