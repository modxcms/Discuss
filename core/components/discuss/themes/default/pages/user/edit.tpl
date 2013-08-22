[[+top]]

<div class="dis-profile">
    <h1>[[%discuss.edit_profile_for? &user=`[[+name]]` &namespace=`discuss` &topic=`user`]]</h1>
    [[!DiscussUpdateProfileLoader]]
    [[!UpdateProfile?
    	&placeholderPrefix=`up`
    	&submitVar=`login-updprof-btn`
    	&postHooks=`postHook.DiscussUpdateProfile`
    ]]

    <form action="[[DiscussUrlMaker? &action=`user/edit` &params=`{"user":"[[+id]]"}`]]" method="post" class="dis-form" id="dis-user-edit-form">
        <input type="hidden" name="user" value="[[+id]]" />
        <label for="dis-name-first">[[%discuss.name_first]]:
            <span class="error">[[+error.name_first]]</span>
        </label>
        <br class="clearfix" />
        <input type="text" name="name_first" id="dis-name-first" value="[[+name_first]]" />
            <br class="clearfix" />
            <label for="dis-name-last">[[%discuss.name_last]]:
                <span class="error">[[+error.name_last]]</span>
            </label>
            <br class="clearfix" />
            <input type="text" name="name_last" id="dis-name-last" value="[[+name_last]]" />
            <br class="clearfix" />
            <label for="dis-email">[[%discuss.email]]:
                <span class="error">[[+error.email]]</span>
            </label>
            <br class="clearfix" />
            <input type="text" name="email" id="dis-email" value="[[+email]]" /><br class="clearfix" />
            <label for="dis-website">[[%discuss.website]]:
                <span class="error">[[+error.website]]</span>
            </label>
            <br class="clearfix" />
            <input type="text" name="website" id="dis-website" value="[[+website]]" />
            <br class="clearfix" />
            <label for="dis-gender">[[%discuss.gender]]:
                <span class="error">[[+error.gender]]</span>
            </label>
            <br class="clearfix" />
            <select name="gender" id="dis-gender">
                [[+genders]]
            </select>
            <br class="clearfix" />
            <label for="dis-birthdate">[[%discuss.birthdate]]:
                <span class="error">[[+error.birthdate]]</span>
            </label>
            <br class="clearfix" />
            <input type="text" name="birthdate" id="dis-birthdate" value="[[+birthdate]]" />
            <br class="clearfix" />
            <label for="dis-location">[[%discuss.location]]:
                <span class="error">[[+error.location]]</span>
            </label>
            <br class="clearfix" />
            <input type="text" name="location" id="dis-location" value="[[+location]]" />
            <br class="clearfix" />
            <label for="dis-title">[[%discuss.custom_title]]:
                <span class="error">[[+error.title]]</span>
            </label>
            <br class="clearfix" />
            <input type="text" name="title" id="dis-title" value="[[+title]]" />
            <br class="clearfix" />
            <label for="dis-title">[[%discuss.avatar_service]]:
                <span class="error">[[+error.title]]</span>
            </label>
            <br class="clearfix" />
            <select name="avatar_service" id="dis-avatar-service" value="[[+avatar_service]]">
                <option value="gravatar">Gravatar</option>
            </select>
            <br class="clearfix" />
            <label for="dis-signature">[[%discuss.signature]]:
                <span class="error">[[+error.signature]]</span>
            </label>
            <br class="clearfix" />
            <textarea type="text" name="signature" id="dis-signature" rows="7" cols="54">
                [[+signature]]
            </textarea>
            <br class="clearfix" />
            <label for="dis-show-email"><input type="checkbox" name="show_email" id="dis-show-email" value="[[+show_email]]">[[%discuss.show_email_public]]
                <span class="error">
                    [[+error.show_email]]
                </span>
            </label>
            <br class="clearfix" />

        <label for="dis-show-online"><input type="checkbox" name="show_online" id="dis-show-online" value="[[+show_online]]">[[%discuss.show_online_status]]
            <span class="error">[[+error.show_online]]</span>
        </label>
        <br class="clearfix" />
        <div class="dis-form-buttons">
            <input type="submit" name="login-updprof-btn" class="dis-action-btn" value="[[%discuss.save_changes? &namespace=`discuss` &topic=`web`]]" />
            <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[DiscussUrlMaker? &action=`user` &params=`{"user":"[[+id]]"}`]]';" />
        </div>
    </form>
</div>



    [[+sidebar]]


</div><!-- Close Content From Wrapper -->
	[[+bottom]]