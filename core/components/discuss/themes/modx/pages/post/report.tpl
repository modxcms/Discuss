<form action="[[~[[*id]]]]post/report?post=[[+id]]" method="post" class="dis-form" id="dis-report-thread-form">

    <h2>[[%discuss.report_to_mod? &namespace=`discuss` &topic=`post`]]</h2>

    <input type="hidden" name="thread" value="[[+id]]" />

    <p>[[%discuss.report_to_mod_confirm? &thread=`[[+title]]`]]</p>


    <label for="dis-report-message">[[%discuss.message]]:
        <span class="error">[[!+fi.error.message]]</span>
    </label>
    <textarea name="message" id="dis-report-message" cols="80" rows="7">[[!+fi.message]]</textarea>
    <br class="clear" />

    <span class="error">[[+error]]</span>

    <br class="clearfix" />

    <div class="dis-form-buttons">
    <input type="submit" name="report-thread" class="dis-action-btn" value="[[%discuss.report_to_mod]]" />
    <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[+url]]';" />
    </div>
</form>