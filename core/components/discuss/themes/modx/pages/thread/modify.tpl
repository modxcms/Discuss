
[[!FormIt?
  &submitVar=`dis-post-modify`
  &hooks=`postHook.DiscussModifyPost`
  &validate=`title:required,message:required:allowTags`
]]



<form action="[[~[[*id]]]]thread/modify?post=[[+id]]" method="post" class="dis-form" id="dis-modify-post-form" enctype="multipart/form-data">
    <div class="preview_toggle">
		<a href="#" class="dis-message-write selected" id="dis-message-write-btn">edit</a>
        <a href="#" class="dis-modify-post-preview-btn" id="dis-modify-post-preview-btn">preview</a>
		<div id="dis-modify-post-preview"></div>
    </div>
		<h1 class="Category">[[%discuss.post_modify? &namespace=`discuss` &topic=`post`]]</h1>
    
    <input type="hidden" name="board" value="[[!+fi.board]]" />
    <input type="hidden" name="post" value="[[!+fi.post]]" />
    <input type="hidden" name="thread" value="[[!+fi.thread]]" />
    
    <label for="dis-new-thread-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label><br class="clearfix" />
    <input type="text" name="title" id="dis-new-thread-title" value="[[!+fi.title]]" /><br class="clearfix" />

    [[+fi.is_root:is=`1`:then=`<label for="dis-new-thread-type">[[%discuss.thread_type]]</label><br class="clearfix" />
    <select name="class_key" id="dis-new-thread-type">
        <option value="disThreadDiscussion" [[+fi.class_key:FormItIsSelected=`disThreadDiscussion`]]>[[%discuss.discussion]]</option>
        <option value="disThreadQuestion" [[+fi.class_key:FormItIsSelected=`disThreadQuestion`]]>[[%discuss.question_and_answer]]</option>
    </select>`]]<br class="clearfix" />

    <div class="wysi-buttons">[[+buttons]]</div>

    
    <label for="dis-thread-message">
        <span class="error">[[!+fi.error.message]]</span>
    </label><br class="clearfix" />
    <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea>
    <br class="clearfix" />
    
    <label for="dis-attachment">[[%discuss.attachments]]:
        <span class="small dis-add-attachment"><a href="javascript:void(0);">[[%discuss.attachment_add]]</a>
        <br />([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span>
        <span class="error">[[+error.attachments]]</span>
    </label>
        <br class="clearfix" />

    <input type="file" name="attachment[[+attachmentCurIdx]]" id="dis-attachment" />
        <br class="clearfix" />

    <div id="dis-attachments"></div>

    [[+attachments:notempty=`<div class="dis-existing-attachments">
        <ul class="dis-attachments">[[+attachments]]</ul>
    </div>`]]

    [[+locked_cb]]
    [[+sticky_cb]]

    <br class="clearfix" />
    <div class="dis-form-buttons">
        <input type="submit" class="Button" name="dis-post-modify" value="[[%discuss.save_changes]]" />
        <input type="button" class="Button" value="[[%discuss.cancel]]" onclick="location.href='[[+url]]';" />
    </div>
</form>


<br class="clearfix" />
<hr />


<div class="dis-thread-posts">
	<h1 class="Category">[[%discuss.thread_summary]]</h1>
[[+thread_posts:default=`<p>[[%discuss.thread_no_posts]]</p>`]]
</div>
[[+discuss.error_panel]]

			</div><!-- Close Content From Wrapper -->
[[+bottom]]

<div id="Panel">
				<hr class="line" />
    <div class="PanelBox">


	[[!$post-sidebar?disection=`new-message`]]

						
						


    </div>