<form class="dis-form dis-login" action="[[DiscussUrlMaker? &action=`login`]]" method="post">
    <h2>[[%discuss.login]]</h2>
    
    <label>[[%discuss.username]]:</label>
    <input type="text" name="username" id="dis-login-username" value="[[+username]]" />
    
    <label>[[%discuss.password]]:</label>
    <input type="password" name="password" id="dis-login-password" value="[[+password]]" />
    
    <br class="clearfix" />
    
    [[+discuss.login_error]]
    
    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="[[%discuss.login]]" />
    <input type="button" class="dis-action-btn" value="[[%discuss.register]]" onclick="location.href='[[DiscussUrlMaker? &action=`register`]]';" />
    </div>
</form>
