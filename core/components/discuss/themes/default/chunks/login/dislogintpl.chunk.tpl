<form class="dis-form dis-login" action="[[DiscussUrlMaker? &action=`login`]]" method="post">
    <input class="loginLoginValue" type="hidden" name="service" value="login" />
    <input class="returnUrl" type="hidden" name="returnUrl" value="[[~[[*id]]]]" />

    <h2>[[%discuss.login? &namespace=`discuss` &topic=`web`]]</h2>

    <label>[[%discuss.username]]:<span class="error">[[+error.username]]</span></label>
    <input type="text" name="username" id="dis-login-username" value="[[+username]]" />

    <label>[[%discuss.password]]:<span class="error">[[+error.password]]</span></label>
    <input type="password" name="password" id="dis-login-password" value="[[+password]]" />

    <br class="clear" />

    <label for="dis-login-rememberme">[[%discuss.rememberme]]:
        <span class="error">[[+error.rememberme]]</span>
    </label>
    <input type="checkbox" name="rememberme" id="dis-login-rememberme" value="1" [[+rememberme:FormItIsChecked=`1`]] />

    [[!+errors]]

    <div class="dis-form-buttons">
        <input type="submit" class="dis-action-btn" value="[[%discuss.login]]" />
        <input type="button" class="dis-action-btn" value="[[%discuss.register]]" onclick="location.href='[[DiscussUrlMaker? &action=`register`]]';" />
    </div>
</form>
