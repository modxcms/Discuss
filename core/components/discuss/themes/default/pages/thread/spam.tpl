<form action="[[DiscussUrlMaker? &action=`thread/spam` &params=`{"thread":"[[+id]]"}`]]" method="post" class="dis-form" id="dis-spam-thread-form">
	<h1>[[%discuss.thread_spam? &namespace=`discuss` &topic=`post`]]</h1>
    <p>[[%discuss.thread_spam_confirm? &thread=`[[+title]]`]]</p>

    <input type="hidden" name="thread" value="[[+id]]" />
    <span class="error">[[+error]]</span>

    <div class="dis-form-buttons">
    	<input type="submit" class="dis-action-btn" value="[[%discuss.thread_spam]]" name="spam-thread" />
	    <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[+url]]';" />
    </div>
</form>

[[+bottom]]

[[+sidebar]]
