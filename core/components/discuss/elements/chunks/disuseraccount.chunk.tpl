
<div class="dis-profile left" style="width: 80%;">

<form action="[[~[[*id]]]]?user=[[+id]]" method="post" class="dis-form" id="dis-user-account-form" style="border: 0;">

    <h2>Edit Account: [[+username]]</h2>
        
    <input type="hidden" name="user" value="[[+id]]" />
    
    <label for="dis-username">User Name:
        <span class="error">[[+error.name_first]]</span>
    </label>
    <input type="text" name="username" id="dis-username" value="[[+username]]" />
    
    <label for="dis-email">Email:
        <span class="error">[[+error.email]]</span>
    </label>
    <input type="text" name="email" id="dis-email" value="[[+email]]" />
    
    <label for="dis-show-email">Show Email to Public?
        <span class="error">[[+error.show_email]]</span>
    </label>
    <input type="checkbox" name="show_email" id="dis-show-email" value="1" [[+show_email]] />
    
    <label for="dis-show-online">Show Online Status?
        <span class="error">[[+error.show_online]]</span>
    </label>
    <input type="checkbox" name="show_online" id="dis-show-online" value="1" [[+show_online]] />
    
    <hr />
    
    
    <label for="dis-password-new">Password:
        <span class="error">[[+error.password_new]]</span>
    </label>
    <input type="password" name="password_new" id="dis-password-new" />
    
    <label for="dis-password-confirm">Confirm Password:
        <span class="error">[[+error.password_confirm]]</span>
    </label>
    <input type="password" name="password_confirm" id="dis-password-confirm" />
    
    
    <hr />
    <br class="clear" />
    
    <p>You will need to provide your current password to change account settings.</p>
    
    <label for="dis-password">Current Password:
        <span class="error">[[+error.password]]</span>
    </label>
    <input type="password" name="password" id="dis-password" />
    
    <br class="clear" />
    
    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="Save Changes" />
    <input type="button" class="dis-action-btn" value="Cancel" onclick="location.href='[[~[[++discuss.user_resource]]]]?user=[[+id]]';" />
    </div>
    
</form>
</div>