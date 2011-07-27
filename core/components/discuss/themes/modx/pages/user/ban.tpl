[[!FormIt?
    &submitVar=`dis-add-ban`
    &hooks=`postHook.DiscussAddBan`
]]



<div class="dis-profile left">




<form action="[[~[[*id]]]]user/ban?u=[[+fi.id]]" method="post" class="dis-form dis-ban-form" id="dis-user-ban-form" style="border: 0;">
	<ul class="DataList CategoryList CategoryListWithHeadings">
	
		<li class="Item CategoryHeading Depth1">
	    <div class="ItemContent Category">[[%discuss.ban_user_header? &username=`[[+fi.username]]`]]</div>
	    </li>
	</ul>

<p style="color: green;">[[+fi.successMessage]]</p>

    <input type="hidden" name="id" value="[[+fi.id]]" />
    <input type="hidden" name="user" value="[[+fi.user]]" />

    <label for="dis-ban-reason">[[%discuss.ban_reason]]:
        <span class="small">[[%discuss.ban_reason_desc]]</span>
        <span class="error">[[+fi.error.reason]]</span>
    </label>
    <textarea name="reason" id="dis-ban-reason" style="width: 300px; height: 80px;">[[+fi.reason]]</textarea>


    <label for="dis-ban-cb-iprange">[[%discuss.ban_iprange]]:
        <span class="small">[[%discuss.ban_iprange_desc]]</span>
        <span class="error">[[+fi.error.ip_range]]</span>
    </label>
    <input type="checkbox" name="cb_ip_range" id="dis-ban-cb-iprange" />
    <input type="text" name="ip_range" id="dis-ban-iprange" value="[[+fi.ip_range]]" />

    <label for="dis-ban-cb-hostname">[[%discuss.ban_hostname]]:
        <span class="small">[[%discuss.ban_hostname_desc]]</span>
        <span class="error">[[+fi.error.hostname]]</span>
    </label>
    <input type="checkbox" name="cb_hostname" id="dis-ban-cb-hostname" />
    <input type="text" name="hostname" id="dis-ban-hostname" value="[[+fi.hostname]]" />

    <label for="dis-ban-cb-email">[[%discuss.ban_email]]:
        <span class="small">[[%discuss.ban_email_desc]]</span>
        <span class="error">[[+fi.error.email]]</span>
    </label>
    <input type="checkbox" name="cb_email" id="dis-ban-cb-email" />
    <input type="text" name="email" id="dis-ban-email" value="[[+fi.email]]" />

    <label for="dis-ban-cb-username">[[%discuss.ban_username]]:
        <span class="small">[[%discuss.ban_username_desc]]</span>
        <span class="error">[[+fi.error.username]]</span>
    </label>
    <input type="checkbox" name="cb_username" id="dis-ban-cb-username" />
    <input type="text" name="username" id="dis-ban-username" value="[[+fi.username]]" />

    <span class="label-inline">
        <span class="label-inline-th">[[%discuss.ban_expireson]]</span>
        <input type="text" name="expireson" id="dis-ban-expireson" value="[[+fi.expireson]]" class="label-inline-text" style="width: 40px;" />
        <span class="label-inline-td">[[%discuss.days]]</span>
        <br class="clear" />
        <span class="small">[[%discuss.ban_expireson_desc]]</span>
        <span class="error">[[+fi.error.expireson]]</span>
    </span>

    <label for="dis-ban-notes">[[%discuss.ban_notes]]:
        <span class="small">[[%discuss.ban_notes_desc]]</span>
        <span class="error">[[+fi.error.notes]]</span>
    </label>
    <textarea name="notes" id="dis-ban-notes" style="width: 300px; height: 80px;">[[+fi.notes]]</textarea>

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
