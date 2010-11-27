[[+trail]]

<form class="dis-form dis-register" action="[[~[[++discuss.register_resource]]]]" method="post">
    <h2>[[%discuss.register? &namespace=`discuss` &topic=`web`]]</h2>
    
    <span class="error">[[+error.spam_empty]]</span>
    <input type="hidden" name="spam_empty" value="" />
    
    <label for="dis-register-username">[[%discuss.username]]:
        <span class="error">[[+error.username]]</span>
    </label>
    <input type="text" name="username" id="dis-register-username" value="[[+username]]" />    
    
    <label for="dis-register-password">[[%discuss.password]]:
        <span class="error">[[+error.password]]</span>
    </label>
    <input type="password" name="password" id="dis-register-password" value="[[+password]]" />
    
    <label for="dis-register-password-confirm">[[%discuss.password_confirm]]:
        <span class="error">[[+error.password_confirm]]</span>
    </label>
    <input type="password" name="password_confirm" id="dis-register-password-confirm" value="[[+password]]" />
    
    <label for="dis-register-email">[[%discuss.email]]:
        <span class="error">[[+error.email]]</span>
    </label>
    <input type="text" name="email" id="dis-register-email" value="[[+email]]" />
    
    <label for="dis-register-show-email">[[%discuss.show_email]]:
        <span class="error">[[+error.show_email]]</span>
    </label>
    <input type="checkbox" name="show_email" id="dis-register-show-email" value="1" [[+show_email]] />

    <div style="padding-left: 140px; clear:both;">
    [[+recaptcha_html]]
    [[+error.recaptcha]]
    </div>

    <br class="clear" />
    
    [[+discuss.login_error]]
    
    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="[[%discuss.register]]" />
    </div>
</form>