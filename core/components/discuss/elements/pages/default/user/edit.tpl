[[+usermenu]]
<div class="dis-profile left" style="width: 80%;">

<form action="[[~[[*id]]]]user/edit?user=[[+id]]" method="post" class="dis-form" id="dis-user-edit-form" style="border: 0;">

    <h2>[[%discuss.edit_profile_for? &user=`[[+username]]` &namespace=`discuss` &topic=`user`]]</h2>
    
    <input type="hidden" name="user" value="[[+id]]" />
    
    <label for="dis-name-first">[[%discuss.name_first]]:
        <span class="error">[[+error.name_first]]</span>
    </label>
    <input type="text" name="name_first" id="dis-name-first" value="[[+name_first]]" />
    
    <label for="dis-name-last">[[%discuss.name_last]]:
        <span class="error">[[+error.name_last]]</span>
    </label>
    <input type="text" name="name_last" id="dis-name-last" value="[[+name_last]]" />
    
    <label for="dis-email">[[%discuss.email]]:
        <span class="error">[[+error.email]]</span>
    </label>
    <input type="text" name="email" id="dis-email" value="[[+email]]" />
    
    <label for="dis-website">[[%discuss.website]]:
        <span class="error">[[+error.website]]</span>
    </label>
    <input type="text" name="website" id="dis-website" value="[[+website]]" />
    
    <label for="dis-gender">[[%discuss.gender]]:
        <span class="error">[[+error.gender]]</span>
    </label>
    <select name="gender" id="dis-gender">
        [[+genders]]
    </select>
    
    <label for="dis-birthdate">[[%discuss.birthdate]]:
        <span class="error">[[+error.birthdate]]</span>
    </label>
    <input type="text" name="birthdate" id="dis-birthdate" value="[[+birthdate]]" />
    
    <label for="dis-location">[[%discuss.location]]:
        <span class="error">[[+error.location]]</span>
    </label>
    <input type="text" name="location" id="dis-location" value="[[+location]]" />
        
    <label for="dis-title">[[%discuss.custom_title]]:
        <span class="error">[[+error.title]]</span>
    </label>
    <input type="text" name="title" id="dis-title" value="[[+title]]" />

    <label for="dis-title">[[%discuss.avatar_service]]:
        <span class="error">[[+error.title]]</span>
    </label>
    <select name="avatar_service" id="dis-avatar-service" value="[[+avatar_service]]">
        <option value="gravatar">Gravatar</option>
    </select>

    <label for="dis-signature">[[%discuss.signature]]:
        <span class="error">[[+error.signature]]</span>
    </label>
    <textarea type="text" name="signature" id="dis-signature" rows="7" cols="54">[[+signature]]</textarea>

    
    <br class="clear" />
    
    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="[[%discuss.save_changes? &namespace=`discuss` &topic=`web`]]" />
    <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]user?user=[[+id]]';" />
    </div>
</form>

</div>
