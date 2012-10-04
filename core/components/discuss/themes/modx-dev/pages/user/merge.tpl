[[!FormIt?
   &hooks=`postHook.DiscussMergeAccount`
   &submitVar=`dis-account-merge`
   &validate=`username:required,password:required`
   &successMessage=``
]]

<div class="dis-profile">



<form action="[[~[[*id]]]]user/merge" method="post" class="dis-form" id="dis-user-merge-form">

	<h1>[[%discuss.account_merge? &namespace=`discuss` &topic=`user`]]: [[+name]]</h1>

    <p>[[%discuss.account_merge_msg]]</p>

    [[+fi.success:notempty=`<p style="color: green; font-weight: bold;">[[%discuss.account_merge_success]]</p>`]]

    <label for="dis-username">[[%discuss.username]]:
        <span class="error">[[+fi.error.username]]</span>
    </label><br class="clearfix" />
    <input type="text" name="username" id="dis-username" value="[[+fi.username]]" />
<br class="clearfix" />
    <label for="dis-password">[[%discuss.password]]:
        <span class="error">[[+fi.error.password]]</span>
    </label><br class="clearfix" />
    <input type="password" name="password" id="dis-password" value="[[+fi.password]]" />

    <br class="clearfix" />

    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" name="dis-account-merge" value="[[%discuss.account_merge]]" />
    <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]user/?user=[[+id]]';" />
    </div>

</form>


</div>
	
[[+sidebar]]
</div><!-- Close Content From Wrapper -->
[[+bottom]]
