<!-- new.tpl -->

[[!FormIt?
  &submitVar=`dis-post-reply`
  &hooks=`postHook.DiscussNewThread`
  &validate=`title:required,message:required:allowTags`
]]



    
<form action="[[~[[*id]]]]thread/new?board=[[+id]]" method="post" class="dis-form" id="dis-new-thread-form" enctype="multipart/form-data">

    <div class="preview_toggle">
		<a href="#" class="dis-message-write selected" id="dis-edit-btn">edit</a>
        <a href="#" class="dis-preview" id="dis-preview-btn">view</a>
    </div>
	<div id="dis-message-preview"></div>

	<h1>[[%discuss.start_new_thread? &namespace=`discuss` &topic=`post`]]</h1>
    
    <input type="hidden" name="board" value="[[+id]]" />
    	
    	<label><input type="radio" name="class_key" value="disThreadDiscussion" checked="checked" /> [[%discuss.discussion]]</label>
    	<label><input type="radio" name="class_key" value="disThreadQuestion" /> [[%discuss.question_and_answer]]</label>

<br class="clearfix" />

    <label for="dis-new-thread-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label><br class="clearfix" />

    <input type="text" name="title" id="dis-new-thread-title" value="[[!+fi.title]]" /><br class="clearfix" />



    
    
<br class="clearfix" />

    <div class="wysi-buttons">[[+buttons]]</div><br class="clearfix" />



        <span class="error">[[!+fi.error.message]]</span>
<br class="clearfix" />
    <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea><br class="clearfix" />

    [[+attachment_fields]]

    <br class="clearfix" />



    <div class="dis-form-buttons">
    [[+locked_cb]]
    [[+sticky_cb]]
    <label class="dis-cb"><input type="checkbox" name="notify" value="1" [[!+fi.notify:FormItIsChecked=`1`]] />[[%discuss.notify_of_replies]]</label><br class="clearfix" />
        <input type="submit" name="dis-post-reply" value="[[%discuss.thread_post_new]]" />
        <input type="button" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]board/?board=[[+id]]';" />
    </div>
</form>
<br class="clearfix" />


[[+discuss.error_panel]]

				</div><!-- Close Content From Wrapper -->

[[+bottom]]



<aside>
				<hr class="line" />
    <div class="PanelBox">
	[[!$post-sidebar?disection=`new-message`]]


</aside>