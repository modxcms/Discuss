<p class="dis-breadcrumbs">[[+trail]]</p>

<div id="dis-modify-post-preview">[[+preview]]</div>
<br />
<form action="[[~[[*id]]]]?post=[[+id]]" method="post" class="dis-form" id="dis-modify-post-form">

    <h2>[[%discuss.post_modify? &namespace=`discuss` &topic=`post`]]</h2>
    
    <input type="hidden" name="board" value="[[+board]]" />
    
    <label for="dis-new-thread-title">[[%discuss.title]]:
        <span class="error">[[+error.title]]</span>
    </label>
    <input type="text" name="title" id="dis-new-thread-title" value="[[+title]]" />
    
    <label for="dis-new-thread-message">[[%discuss.message]]:
        <span class="error">[[+error.message]]</span>
    </label>
    <textarea name="message" id="dis-new-thread-message" cols="80" rows="7">[[+message]]</textarea>
    <br class="clear" />
    
   
    <label class="dis-cb"><input type="checkbox" name="locked" value="1" />[[%discuss.thread_lock? &namespace=`discuss` &topic=`web`]]</label>
    <label class="dis-cb"><input type="checkbox" name="sticky" value="1" />[[%discuss.thread_sticky]]</label>

    <br class="clear" />
    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="[[%discuss.save_changes]]" />
    <input type="button" class="dis-action-btn" id="dis-new-thread-preview-btn" value="[[%discuss.preview]]" onclick="DISModifyPost.preview();" />
    <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[++discuss.thread_resource]]]]?thread=[[+thread]]';" />
    </div>
</form>

<br />
<hr />
<div class="dis-thread-posts">
    <h2>[[%discuss.thread_summary]]</h2>
[[+thread_posts:default=`<p>[[%discuss.thread_no_posts]]</p>`]]
</div>
[[+discuss.error_panel]]