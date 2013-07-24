<form action="[[DiscussUrlMaker? &action=`thread/remove` &params=`{"thread":"[[+id]]"}`]]" method="post" class="dis-form" id="dis-remove-thread-form">
	<h1>[[%discuss.thread_remove? &namespace=`discuss` &topic=`post`]]</h1>
    <p>[[%discuss.thread_remove_confirm? &thread=`[[+title]]`]]</p>
        
    <input type="hidden" name="thread" value="[[+id]]" />
    <span class="error">[[+error]]</span>
    
    <div class="dis-form-buttons">
	    <input type="submit" class="dis-action-btn" value="[[%discuss.thread_remove]]" name="remove-thread"/>
    	<input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[+url]]';" />
    </div>
</form>

[[+bottom]]

[[+sidebar]]
