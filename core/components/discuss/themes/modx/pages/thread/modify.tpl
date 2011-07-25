
[[!FormIt?
  &submitVar=`dis-post-modify`
  &hooks=`postHook.DiscussModifyPost`
  &validate=`title:required,message:required:allowTags`
]]

<div id="dis-modify-post-preview">[[+preview]]</div>
<br />
<form action="[[~[[*id]]]]thread/modify?post=[[+id]]" method="post" class="dis-form" id="dis-modify-post-form" enctype="multipart/form-data">

    <h2>[[%discuss.post_modify? &namespace=`discuss` &topic=`post`]]</h2>
    
    <input type="hidden" name="board" value="[[!+fi.board]]" />
    <input type="hidden" name="post" value="[[!+fi.post]]" />
    <input type="hidden" name="thread" value="[[!+fi.thread]]" />
    
    <label for="dis-new-thread-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label>
    <input type="text" name="title" id="dis-new-thread-title" value="[[!+fi.title]]" />

    <div class="wysi-buttons">[[+buttons]]</div>

    
    <label for="dis-thread-message">
        <span class="error">[[!+fi.error.message]]</span>
    </label>
    <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea>
    <br class="clear" />

    [[+attachment_fields]]
    <br class="clear" />

    [[+locked_cb]]
    [[+sticky_cb]]

    <br class="clear" />
    <div class="dis-form-buttons">
        <input type="submit" class="Button" name="dis-post-modify" value="[[%discuss.save_changes]]" />
        <input type="button" class="Button dis-modify-post-preview-btn" id="dis-modify-post-preview-btn" value="[[%discuss.preview]]" />
        <input type="button" class="Button" value="[[%discuss.cancel]]" onclick="location.href='[[+url]]';" />
    </div>
</form>

<br />
<hr />
<div class="dis-thread-posts">
    <h2>[[%discuss.thread_summary]]</h2>
[[+thread_posts:default=`<p>[[%discuss.thread_no_posts]]</p>`]]
</div>
[[+discuss.error_panel]]