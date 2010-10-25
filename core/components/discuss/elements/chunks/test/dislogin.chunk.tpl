<form class="board-form" action="[[~[[++discuss.login_resource]]]]" method="post" id="login-form">
   <h3 class="category_title">[[%discuss.login]]</h3>
   
   <fieldset> 
		<label>[[%discuss.username]]:</label>
		<input type="text" name="username" id="dis-login-username" value="[[+username]]" />
		
		<label>[[%discuss.password]]:</label>
		<input type="password" name="password" id="dis-login-password" value="[[+password]]" />
		
		[[+discuss.login_error]]
		
		<div class="btns">
			<input type="submit" class="board-btns" value="[[%discuss.login]]" />
			<input type="button" class="board-btns" value="[[%discuss.register]]" onclick="location.href='[[~[[++discuss.register_resource]]]]';" />	
		</div>
	</fieldset>
</form>