<p class="dis-breadcrumbs">[[+trail]]</p>

<div id="dis-reply-post-preview">[[+preview]]</div>
<br />
<form action="[[~[[*id]]]]?post=[[+id]]" method="post" class="dis-form" id="dis-reply-post-form" enctype="multipart/form-data">

    <h2>Reply to Post</h2>
    
    <input type="hidden" name="board" value="[[+board]]" />
    <input type="hidden" name="post" value="[[+id]]" />
    
    <label for="dis-reply-post-title">Title:
        <span class="error">[[+error.title]]</span>
    </label>
    <input type="text" name="title" id="dis-reply-post-title" value="[[+post.title]]" />
    
    <label for="dis-reply-post-message">Message:
        <span class="error">[[+error.message]]</span>
    </label>
    <textarea name="message" id="dis-reply-post-message" cols="80" rows="7">[[+post.message]]</textarea>
    <br class="clear" />
    
    <label for="dis-reply-post-attachments">Attachments:
        <span class="small dis-reply-post-add-attachment"><a href="[[~[[*id]]]]?post=[[+id]]">Add Attachment</a></a>
        <span class="error">[[+error.attachment1]]</span>
    </label>
    <input type="file" name="attachment1" id="dis-reply-post-attachment" />
    
    
    
    <div id="dis-attachments"></div>
    <br class="clear" />
    
    <label class="dis-cb"><input type="checkbox" name="notify" value="1" />Notify of Replies</label>
    <label class="dis-cb"><input type="checkbox" name="locked" value="1" />Lock Post</label>
    
    <br class="clear" />
    
    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="Post Reply" />
    <input type="button" class="dis-action-btn dis-reply-post-preview" value="Preview" />
    <input type="button" class="dis-action-btn" value="Cancel" onclick="location.href='[[~[[++discuss.thread_resource]]]]?thread=[[+thread]]';" />
    </div>
</form>

<br />
<hr />
<div class="dis-thread-posts">
    <h2>Thread Summary</h2>
[[+thread_posts]]
</div>

[[+discuss.error_panel]]