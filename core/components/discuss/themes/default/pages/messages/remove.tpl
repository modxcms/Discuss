[[+trail]]

<br />
<form action="[[DiscussUrlMaker? &action=`messages/remove` &params=`{"thread":"[[+id]]"}`]]" method="post" class="dis-form" id="dis-remove-message-form">

    <h2>[[%discuss.message_remove? &namespace=`discuss` &topic=`post`]]</h2>

    <input type="hidden" name="thread" value="[[+id]]" />

    <p>[[%discuss.message_remove_confirm? &thread=`[[+title]]`]]</p>

    <span class="error">[[+error]]</span>

    <br class="clear" />

    <div class="dis-form-buttons">
    <input type="submit" name="remove-message" class="dis-action-btn" value="[[%discuss.message_remove]]" />
    <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[DiscussUrlMaker? &action=`messages/view` &params=`{"message":"[[+id]]"}`]]';" />
    </div>
</form>