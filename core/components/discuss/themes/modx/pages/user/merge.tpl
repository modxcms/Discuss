[[!FormIt?
   &hooks=`postHook.DiscussMergeAccount`
   &submitVar=`dis-account-merge`
   &validate=`username:required,password:required`
   &successMessage=``
]]

<div class="dis-profile left">



<form action="[[~[[*id]]]]user/merge" method="post" class="dis-form" id="dis-user-merge-form" style="border: 0;">

    <h2>[[%discuss.account_merge? &namespace=`discuss` &topic=`user`]]: [[+name]]</h2>

    <p>[[%discuss.account_merge_msg]]</p>

    [[+fi.success:notempty=`<p style="color: green; font-weight: bold;">[[%discuss.account_merge_success]]</p>`]]

    <label for="dis-username">[[%discuss.username]]:
        <span class="error">[[+fi.error.username]]</span>
    </label>
    <input type="text" name="username" id="dis-username" value="[[+fi.username]]" />

    <label for="dis-password">[[%discuss.password]]:
        <span class="error">[[+fi.error.password]]</span>
    </label>
    <input type="password" name="password" id="dis-password" value="[[+fi.password]]" />

    <br class="clearfix" />

    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" name="dis-account-merge" value="[[%discuss.account_merge]]" />
    <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]user/?user=[[+id]]';" />
    </div>

</form>


</div>
	

</div><!-- Close Content From Wrapper -->
	[[+bottom]]


				<div id="Panel">
					<div class="PanelBox">
					
						<div class="Box GuestBox">
						   <h4>[[+name]]'s Profile</h4>
							<ul class="PanelInfo PanelCategories">

								<li class="Heading"><img src="[[+avatarUrl]]" alt="[[+username]]" />
							<br /><span class="small">[[+title]]</span></li>
							</ul>
							
						</div>
						
						<div class="Box BoxCategories">
							[[+usermenu]]


						</div>
					</div>
