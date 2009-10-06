
<div class="dis-profile left" style="width: 80%;">

<form action="[[~[[*id]]]]?user=[[+id]]" method="post" class="dis-form" id="dis-user-edit-form" style="border: 0;">

    <h2>Edit Profile</h2>
    
    <input type="hidden" name="user" value="[[+id]]" />
    
    <label for="dis-name-first">First Name:
        <span class="error">[[+error.name_first]]</span>
    </label>
    <input type="text" name="name_first" id="dis-name-first" value="[[+name_first]]" />
    
    <label for="dis-name-last">Last Name:
        <span class="error">[[+error.name_last]]</span>
    </label>
    <input type="text" name="name_last" id="dis-name-last" value="[[+name_last]]" />
    
    <label for="dis-email">Email:
        <span class="error">[[+error.email]]</span>
    </label>
    <input type="text" name="email" id="dis-email" value="[[+email]]" />
    
    <label for="dis-website">Website:
        <span class="error">[[+error.website]]</span>
    </label>
    <input type="text" name="website" id="dis-website" value="[[+website]]" />
    
    <label for="dis-gender">Gender:
        <span class="error">[[+error.gender]]</span>
    </label>
    <select name="gender" id="dis-gender">
        [[+genders]]
    </select>
    
    <label for="dis-birthdate">Birthday:
        <span class="error">[[+error.birthdate]]</span>
    </label>
    <input type="text" name="birthdate" id="dis-birthdate" value="[[+birthdate]]" />
    
    <label for="dis-location">Location:
        <span class="error">[[+error.location]]</span>
    </label>
    <input type="text" name="location" id="dis-location" value="[[+location]]" />
        
    <label for="dis-title">Custom Title:
        <span class="error">[[+error.title]]</span>
    </label>
    <input type="text" name="title" id="dis-title" value="[[+title]]" />
    
    <label for="dis-signature">Signature:
        <span class="error">[[+error.signature]]</span>
    </label>
    <textarea type="text" name="signature" id="dis-signature" rows="7" cols="54">[[+signature]]</textarea>
    
    <br class="clear" />
    
    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="Save Changes" />
    <input type="button" class="dis-action-btn" value="Cancel" onclick="location.href='[[~[[++discuss.user_resource]]]]?user=[[+id]]';" />
    </div>
</form>

</div>
