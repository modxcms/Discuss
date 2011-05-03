[[+trail]]

<br />
<form action="[[~[[*id]]]]thread/spam?thread=[[+id]]" method="post" class="dis-form" id="dis-spam-thread-form">

    <h2>[[%discuss.thread_spam? &namespace=`discuss` &topic=`post`]]</h2>

    <input type="hidden" name="thread" value="[[+id]]" />

    <p>[[%discuss.thread_spam_confirm? &thread=`[[+title]]`]]</p>

    <span class="error">[[+error]]</span>

    <br class="clear" />

    <div class="dis-form-buttons">
    <input type="submit" name="spam-thread" class="dis-action-btn" value="[[%discuss.thread_spam]]" />
    <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]thread?thread=[[+id]]';" />
    </div>
</form>