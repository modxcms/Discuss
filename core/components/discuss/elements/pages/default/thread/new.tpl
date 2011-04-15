[[+trail]]

[[!FormIt?
  &submitVar=`dis-post-new`
  &hooks=`postHook.DiscussNewThread`
  &validate=`title:required,message:required:allowTags`
]]


<div id="dis-new-thread-preview">[[+preview]]</div>
<br />
<form action="[[~[[*id]]]]thread/new?board=[[+id]]" method="post" class="dis-form" id="dis-new-thread-form" enctype="multipart/form-data">

    <h2>[[%discuss.start_new_thread? &namespace=`discuss` &topic=`post`]]</h2>
    
    <input type="hidden" name="board" value="[[+id]]" />
    
    <label for="dis-new-thread-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label>
    <input type="text" name="title" id="dis-new-thread-title" value="[[!+fi.title]]" />

    <div style="margin-left: 150px;">
        <br class="clear" />
        [[+buttons]]
        <br class="clear" />
    </div>

    <label for="dis-thread-message">[[%discuss.message]]:
        <span class="error">[[!+fi.error.message]]</span>
    </label>
    <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea>
    
    <label for="dis-new-thread-attachment">[[%discuss.attachments]]:
        <span class="small dis-new-thread-add-attachment"><a href="[[~[[*id]]]]board/?board=[[+id]]">[[%discuss.attachment_add]]</a>
        <br />([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span>
        <span class="error">[[+error.attachments]]</span>
    </label>
    <input type="file" name="attachment1" id="dis-new-thread-attachment" />
    
    <div id="dis-attachments"></div>
    <br class="clear" />
            
    <label class="dis-cb"><input type="checkbox" name="locked" value="1" [[!+fi.locked:FormItIsChecked=`1`]] />[[%discuss.thread_lock? &namespace=`discuss` &topic=`web`]]</label>
    <label class="dis-cb"><input type="checkbox" name="sticky" value="1" [[!+fi.sticky:FormItIsChecked=`1`]] />[[%discuss.thread_stick]]</label>
    <br class="clear" />
    <label class="dis-cb"><input type="checkbox" name="notify" value="1" [[!+fi.notify:FormItIsChecked=`1`]] />[[%discuss.notify_of_replies]]</label>
    <br class="clear" />

    <div class="dis-form-buttons">
        <input type="submit" class="dis-action-btn" name="dis-post-new" value="[[%discuss.thread_post_new]]" />
        <input type="button" class="dis-action-btn dis-new-thread-preview" id="dis-new-thread-preview-btn" value="[[%discuss.preview]]" />
        <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]board/?board=[[+id]]';" />
    </div>
</form>
[[+discuss.error_panel]]