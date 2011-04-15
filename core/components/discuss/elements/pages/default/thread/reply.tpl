[[+trail]]

[[!FormIt?
  &submitVar=`dis-post-reply`
  &hooks=`postHook.DiscussReplyPost`
  &validate=`title:required,message:required:allowTags`
]]

<div id="dis-reply-post-preview">[[+preview]]</div>
<br />
<form action="[[~[[*id]]]]thread/reply?post=[[+id]]" method="post" class="dis-form" id="dis-reply-post-form" enctype="multipart/form-data">

    <h2>[[%discuss.post_reply? &namespace=`discuss` &topic=`post`]]</h2>
    
    <input type="hidden" name="board" value="[[!+fi.board]]" />
    <input type="hidden" name="thread" value="[[!+fi.thread]]" />
    <input type="hidden" name="post" value="[[!+fi.post]]" />
    
    <label for="dis-reply-post-title">[[%discuss.title]]:
        <span class="error">[[!+fi.error.title]]</span>
    </label>
    <input type="text" name="title" id="dis-reply-post-title" value="[[!+fi.title]]" />
    
    <label for="dis-reply-post-message">[[%discuss.message]]:
        <span class="error">[[!+fi.error.message]]</span>
    </label>
    <textarea name="message" id="dis-reply-post-message" cols="80" rows="7">[[+post.message]]</textarea>
    <br class="clear" />
    
    <label for="dis-reply-post-attachment">[[%discuss.attachments]]:
        <span class="small dis-reply-post-add-attachment"><a href="[[~[[*id]]]]?post=[[+id]]">[[%discuss.attachment_add]]</a>
        <br />([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span>
        <span class="error">[[+error.attachments]]</span>
    </label>
    <input type="file" name="attachment1" id="dis-reply-post-attachment" />    
    
    <div id="dis-attachments"></div>
    <br class="clear" />
    
    <label class="dis-cb"><input type="checkbox" name="locked" value="1" />[[%discuss.thread_lock? &namespace=`discuss` &topic=`web`]]</label>
    <label class="dis-cb"><input type="checkbox" name="sticky" value="1" />[[%discuss.thread_stick]]</label>
    <label class="dis-cb"><input type="checkbox" name="notify" value="1" />[[%discuss.notify_of_replies]]</label>
    
    <br class="clear" />
    
    <div class="dis-form-buttons">
        <input type="submit" class="dis-action-btn" name="dis-post-reply" value="[[%discuss.post_reply]]" />
        <input type="button" class="dis-action-btn dis-reply-post-preview" name="dis-post-preview" value="[[%discuss.preview]]" />
        <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]thread/?thread=[[+thread]]';" />
    </div>
</form>

<br />
<hr />
<div class="dis-thread-posts">
    <h2>[[%discuss.thread_summary]]</h2>
[[+thread_posts]]
</div>

[[+discuss.error_panel]]