<form action="[[DiscussUrlMaker? &action=`messages/remove` &params=`{"thread":"[[+id]]"}`]]" method="post" class="dis-form" id="dis-remove-message-form">
	<h1>[[%discuss.message_remove? &namespace=`discuss` &topic=`post`]]</h1>
    <p>[[%discuss.message_remove_confirm? &thread=`[[+title]]`]]</p>

    <input type="hidden" name="thread" value="[[+id]]" />
    <span class="error">[[+error]]</span>

    <div class="dis-form-buttons">
        <input type="submit" class="dis-action-btn" value="[[%discuss.message_remove]]" name="remove-message" />
        <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[DiscussUrlMaker? &action=`messages/view` &params=`{"message":"[[+id]]"}`]]';" />
    </div>
</form>

[[+bottom]]

[[+sidebar]]
