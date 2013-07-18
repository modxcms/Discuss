<form action="[[DiscussUrlMaker? &action=`post/report` &params=`{"post":"[[+id]]"}`]]" method="post" class="dis-form" id="dis-report-thread-form">
    <h1>[[%discuss.report_to_mod? &namespace=`discuss` &topic=`post`]]</h1>
    <p>[[%discuss.report_to_mod_confirm? &thread=`[[+title]]`]]</p>

    <input type="hidden" name="thread" value="[[+thread]]" />

    <label for="dis-report-message">[[%discuss.message]]:
        <span class="error">[[!+fi.error.message]]</span>
    </label>
    <textarea name="message" id="dis-report-message" cols="80" rows="7">[[!+fi.message]]</textarea>
    <span class="error">[[+error]]</span>

    <div class="dis-form-buttons">
        <input type="submit" class="dis-action-btn" value="[[%discuss.report_to_mod]]" name="report-thread" />
        <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[+url]]';" />
    </div>
</form>

[[+bottom]]

[[+sidebar]]
