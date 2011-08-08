[[+usermenu]]
<div class="dis-profile left">

<form action="[[~[[*id]]]]user/account?user=[[+id]]" method="post" id="dis-user-account-form" style="border: 0;">

	<h1 class="Category">[[%discuss.account_edit? &namespace=`discuss` &topic=`user`]]: [[+name]]</h1>

        
    <input type="hidden" name="user" value="[[+id]]" />
    
    <label for="dis-username">[[%discuss.username]]:
        <span class="error">[[+error.name_first]]</span>
    </label>
    <input type="text" name="username" id="dis-username" value="[[+username]]" />
    
    <label for="dis-email">[[%discuss.email]]:
        <span class="error">[[+error.email]]</span>
    </label>
    <input type="text" name="email" id="dis-email" value="[[+email]]" />
    
    <hr />
    
    
    <label for="dis-password-new">[[%discuss.password]]:
        <span class="error">[[+error.password_new]]</span>
    </label>
    <input type="password" name="password_new" id="dis-password-new" />
    
    <label for="dis-password-confirm">[[%discuss.password_confirm]]:
        <span class="error">[[+error.password_confirm]]</span>
    </label>
    <input type="password" name="password_confirm" id="dis-password-confirm" />
    
    
    <hr />
    <br class="clear" />
    
    <p>[[%discuss.provide_current_password]]</p>
    
    <label for="dis-password">[[%discuss.password_current]]:
        <span class="error">[[+error.password]]</span>
    </label>
    <input type="password" name="password" id="dis-password" />
    
    <br class="clear" />
    
    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="[[%discuss.save_changes? &namespace=`discuss` &topic=`web`]]" />
    <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]user/?user=[[+id]]';" />
    </div>
    
</form>
</div>