[[+trail]]

[[!FormIt?
  &submitVar=`dis-post-new`
  &hooks=`postHook.DiscussNewThread`
  &validate=`title:required,message:required:allowTags`
]]


<div id="dis-new-thread-preview"></div>
<br />
<form action="[[~[[*id]]]]thread/new?board=[[+id]]" method="post" class="dis-form" id="dis-new-thread-form" enctype="multipart/form-data">

    <h2>[[%discuss.start_new_thread? &namespace=`discuss` &topic=`post`]]</h2>
    
    <input type="hidden" name="board" value="[[+id]]" />
    
    <label for="dis-new-thread-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label>
    <input type="text" name="title" id="dis-new-thread-title" value="[[!+fi.title]]" />

    <label for="dis-new-thread-type">[[%discuss.thread_type]]</label>
    <select name="class_key" id="dis-new-thread-type">
        <option value="disThreadDiscussion">[[%discuss.discussion]]</option>
        <option value="disThreadQuestion">[[%discuss.question_and_answer]]</option>
    </select>

    <div class="wysi-buttons">[[+buttons]]</div>


    <label for="dis-thread-message">[[%discuss.message]]:
        <span class="error">[[!+fi.error.message]]</span>
    </label>
    <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea>

    [[+attachment_fields]]

    <br class="clear" />


    [[+locked_cb]]
    [[+sticky_cb]]

    <label class="dis-cb"><input type="checkbox" name="notify" value="1" [[!+fi.notify:FormItIsChecked=`1`]] />[[%discuss.notify_of_replies]]</label><br class="clear" />

    <div class="dis-form-buttons">
        <input type="submit" class="Button" name="dis-post-new" value="[[%discuss.thread_post_new]]" />
        <input type="button" class="Button dis-new-thread-preview" id="dis-new-thread-preview-btn" value="[[%discuss.preview]]" />
        <input type="button" class="Button" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]board/?board=[[+id]]';" />
    </div>
</form>
[[+discuss.error_panel]]