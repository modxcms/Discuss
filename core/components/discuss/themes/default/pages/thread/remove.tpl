[[+trail]]

<br />
<form action="[[DiscussUrlMaker? &action=`thread/remove` &params=`{"thread":"[[+id]]"}`]]" method="post" class="dis-form" id="dis-remove-thread-form">

    <h2>[[%discuss.thread_remove? &namespace=`discuss` &topic=`post`]]</h2>
    
    <input type="hidden" name="thread" value="[[+id]]" />
    
    <p>[[%discuss.thread_remove_confirm? &thread=`[[+title]]`]]</p>
    
    <span class="error">[[+error]]</span>
    
    <br class="clear" />
    
    <div class="dis-form-buttons">
    <input type="submit" name="remove-thread" class="dis-action-btn" value="[[%discuss.thread_remove]]" />
    <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[+url]]';" />
    </div>
</form>