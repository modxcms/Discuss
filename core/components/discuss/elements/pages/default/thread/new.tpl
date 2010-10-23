<p class="dis-breadcrumbs">[[+trail]]</p>

<div id="dis-new-thread-preview">[[+preview]]</div>
<br />
<form action="[[~[[*id]]? &board=`[[+board]]`]]" method="post" class="dis-form" id="dis-new-thread-form">

    <h2>[[%discuss.start_new_thread? &namespace=`discuss` &topic=`post`]]</h2>
    
    <input type="hidden" name="board" value="[[+board]]" />
    
    <label for="dis-new-thread-title">[[%discuss.title]]:
        <span class="error">[[+error.title]]</span>
    </label>
    <input type="text" name="title" id="dis-new-thread-title" value="" />
    
    <label for="dis-new-thread-message">[[%discuss.message]]:
        <span class="error">[[+error.message]]</span>
    </label>
    <textarea name="message" id="dis-new-thread-message" cols="80" rows="7"></textarea>
    
    <label for="dis-new-thread-attachments">[[%discuss.attachments]]:
        <span class="small dis-new-thread-add-attachment"><a href="[[~[[*id]]? &board=`[[+board]]`]]">[[%discuss.attachment_add]]</a>
        <br />([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span>
        <span class="error">[[+error.attachments]]</span>
    </label>
    <input type="file" name="attachment1" id="dis-new-thread-attachment" />    
    
    <div id="dis-attachments"></div>
    <br class="clear" />
            
    <label class="dis-cb"><input type="checkbox" name="locked" value="1" />[[%discuss.thread_lock? &namespace=`discuss` &topic=`web`]]</label>
    <label class="dis-cb"><input type="checkbox" name="sticky" value="1" />[[%discuss.thread_stick]]</label>
    <br class="clear" />
    <label class="dis-cb"><input type="checkbox" name="notify" value="1" />[[%discuss.notify_of_replies]]</label>
    <br class="clear" />

    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="[[%discuss.thread_post_new]]" />
    <input type="button" class="dis-action-btn dis-new-thread-preview" id="dis-new-thread-preview-btn" value="[[%discuss.preview]]" />
    <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[++discuss.board_resource]]? &board=`[[+board]]`]]';" />
    </div>
</form>
[[+discuss.error_panel]]