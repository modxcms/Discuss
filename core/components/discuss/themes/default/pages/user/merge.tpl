[[!FormIt?
   &hooks=`postHook.DiscussMergeAccount`
   &submitVar=`dis-account-merge`
   &validate=`username:required,password:required`
   &successMessage=``
]]
[[+usermenu]]
<div class="dis-profile left" style="width: 80%;">

<form action="[[~[[*id]]]]user/merge" method="post" class="dis-form" id="dis-user-merge-form" style="border: 0;">

    <h2>[[%discuss.account_merge? &namespace=`discuss` &topic=`user`]]: [[+username]]</h2>

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

    <br class="clear" />

    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" name="dis-account-merge" value="[[%discuss.account_merge]]" />
    <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]user/?user=[[+id]]';" />
    </div>

</form>
</div>