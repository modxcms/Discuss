<li class="dis-post group-fix">
    <input type="button" name="Cancel" value="Edit" class="dis-message-cancel Button l-right">
    <div class="dis-post-left">
        <ul>
           <!-- Remove/Merge depending if DiscussUrlMaker works directly <li class="dis-usr-icon"><a href="[[~[[*id]]? &scheme=`full`]]u/[[+author.username]]" class="auth-avatar" title="[[%discuss.view_author_profile]]">[[+author.avatar]]</a></li>-->
            <li class="dis-usr-icon"><a href="[[DiscussUrlMaker? &action=`user` &params=`{"user":"[[+author.id]]"}`]]" class="auth-avatar" title="[[%discuss.view_author_profile]]">[[+author.avatar]]</a></li>
            <li class="dis-usr-post-count">[[+author.posts]] [[%discuss.posts]]</li>
        </ul>
    </div>

    <div class="dis-post-right">
        <div class="title">
            <strong>[[+author.username_link]]</strong> <a class="normal-type" href="[[+url]]" title="[[%discuss.post_link]]">Reply #[[+idx]]</a>, <span title="[[+createdon]]">[[+createdon:ago]]</span>
        </div>
        <div class="dis-content">
            [[+message]]
        </div>
    </div>
    [[+author.signature:notempty=`<div class="dis-signature">[[+author.signature]]</div>`]]
    <div class="dis-post-footer">
        [[+attachments:notempty=`<div class="dis-post-attachments"><ul class="dis-attachments">[[+attachments]]</ul></div>`]]
    </div>
</li>