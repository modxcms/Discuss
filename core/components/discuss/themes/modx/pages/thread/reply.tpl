[[+trail]]

[[!FormIt?
  &submitVar=`dis-post-reply`
  &hooks=`postHook.DiscussReplyPost`
  &validate=`title:required,message:required:allowTags`
]]

<div id="dis-reply-post-preview"></div>
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


    <div class="wysi-buttons">[[+buttons]]</div>

    
    <label for="dis-thread-message">[[%discuss.message]]:
        <span class="error">[[!+fi.error.message]]</span>
    </label>
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
        <input type="button" name="dis-post-preview" value="[[%discuss.preview]]" />
        <input type="button" value="[[%discuss.cancel]]" onclick="location.href='[[+url]]';" />
    </div>
</form>

<br />
<hr />
<div class="dis-thread-posts">
    <h2>[[%discuss.thread_summary]]</h2>
[[+thread_posts]]
</div>

[[+discuss.error_panel]]