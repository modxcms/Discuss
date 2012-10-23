[[-<li class="dis-post">
    <div class="dis-post-header">
		<!-- <h1>[[+title]]</h1> -->
        <input type="button" name="Cancel" value="Edit" class="dis-message-cancel Button l-right">
        <div class="dis-post-author">
            <div class="auth-avatar">[[+author.avatar]]</div>
		</div>
	</div>

    <div class="dis-post-content">
        <h4 class="created">[[+author.username]] [[+createdon:ago]]</h4>
        <div>[[+message]]</div>
        <div class="dis-post-ct" id="dis-thread-ct-[[+id]]">
            <div class="dis-post-footer">
                <div class="dis-post-reply" id="dis-post-reply-[[+id]]"></div>
                <div class="dis-post-attachments">
                [[+attachments:notempty=`<ul class="dis-attachments">[[+attachments]]</ul>`]]
                </div>
            </div>
        </div>
    </div>
    <br class="clearfix" />
</li>]]

<li class="dis-post group-fix">
    <input type="button" name="Cancel" value="Edit" class="dis-message-cancel Button l-right">
    <div class="dis-post-left">
        <ul>
            <li class="dis-usr-icon"><a href="[[~[[*id]]? &scheme=`full`]]u/[[+author.username]]" class="auth-avatar" title="[[%discuss.view_author_profile]]">[[+author.avatar]]</a></li>
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