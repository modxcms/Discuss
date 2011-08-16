[[!FormIt?
    &submitVar=`dis-add-ban`
    &hooks=`postHook.DiscussAddBan`
]]



<div class="dis-profile left">




<form action="[[~[[*id]]]]user/ban?u=[[+fi.id]]" method="post" class="dis-form dis-ban-form" id="dis-user-ban-form">

	<h1 class="Category">[[%discuss.ban_user_header? &username=`[[+fi.username]]`]]</h1>

<p>[[+fi.successMessage]]</p>

    <input type="hidden" name="id" value="[[+fi.id]]" />
    <input type="hidden" name="user" value="[[+fi.user]]" />
    <br class="clearfix" />
    <label for="dis-ban-reason">[[%discuss.ban_reason]]:
        <span class="small">[[%discuss.ban_reason_desc]]</span>
        <span class="error">[[+fi.error.reason]]</span>
    </label>
    <br class="clearfix" />
    <textarea name="reason" id="dis-ban-reason">[[+fi.reason]]</textarea>
    <br class="clearfix" />

    <label for="dis-ban-cb-iprange">[[%discuss.ban_iprange]]:
        <span class="small">[[%discuss.ban_iprange_desc]]</span>
        <span class="error">[[+fi.error.ip_range]]</span>
    </label>
    <br class="clearfix" />
    <input type="checkbox" name="cb_ip_range" id="dis-ban-cb-iprange" />
    <input type="text" name="ip_range" id="dis-ban-iprange" value="[[+fi.ip_range]]" />
    
    <br class="clearfix" />
    
    <label for="dis-ban-cb-hostname">[[%discuss.ban_hostname]]:
        <span class="small">[[%discuss.ban_hostname_desc]]</span>
        <span class="error">[[+fi.error.hostname]]</span>
    </label>
    <br class="clearfix" />
    <input type="checkbox" name="cb_hostname" id="dis-ban-cb-hostname" />
    <input type="text" name="hostname" id="dis-ban-hostname" value="[[+fi.hostname]]" />
    
    <br class="clearfix" />
    
    <label for="dis-ban-cb-email">[[%discuss.ban_email]]:
        <span class="small">[[%discuss.ban_email_desc]]</span>
        <span class="error">[[+fi.error.email]]</span>
    </label>
    <br class="clearfix" />
    <input type="checkbox" name="cb_email" id="dis-ban-cb-email" />
    <input type="text" name="email" id="dis-ban-email" value="[[+fi.email]]" />
    
    <br class="clearfix" />
    
    <label for="dis-ban-cb-username">[[%discuss.ban_username]]:
        <span class="small">[[%discuss.ban_username_desc]]</span>
        <span class="error">[[+fi.error.username]]</span>
    </label>
    <br class="clearfix" />
    <input type="checkbox" name="cb_username" id="dis-ban-cb-username" />
    <input type="text" name="username" id="dis-ban-username" value="[[+fi.username]]" />
    
    <br class="clearfix" />
    
    <span class="label-inline">
        <label for="dis-ban-cb-username"><span class="label-inline-th">[[%discuss.ban_expireson]]</span>
        <span class="label-inline-td">[[%discuss.days]]</span>
        <span class="small">[[%discuss.ban_expireson_desc]]</span></label>
    <br class="clearfix" />
        <input type="text" name="expireson" id="dis-ban-expireson" value="[[+fi.expireson]]" class="label-inline-text" />
        <span class="error">[[+fi.error.expireson]]</span>
    </span>
    
    <br class="clearfix" />
    <label for="dis-ban-notes">[[%discuss.ban_notes]]:
        <span class="small">[[%discuss.ban_notes_desc]]</span>
        <span class="error">[[+fi.error.notes]]</span>
    </label>
    <br class="clearfix" />
    <textarea name="notes" id="dis-ban-notes">[[+fi.notes]]</textarea>

    [[+other_fields]]

    <br class="clearfix" />

    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" name="dis-add-ban" value="[[%discuss.ban_add? &namespace=`discuss` &topic=`web`]]" />
    <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]user?user=[[+id]]';" />
    </div>
</form>

</div>
	

</div><!-- Close Content From Wrapper -->
	[[+bottom]]


<div id="Panel">
				<hr class="line" />
    <div class="PanelBox">


		[[$user-sidebar]]


    </div>