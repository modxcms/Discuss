<li class="[[+class]] group-fix" id="dis-post-[[+id]]" data-author="[[+author.username:htmlent]]" data-date="[[+createdon_raw]]" data-message="[[+content_raw]]">
    [[+answer:notempty=`
    <div class="dis-post-answer-marker">
        <!-- load this only if more than one answer?--><nav><a href="#">Previous</a><a class="next" href="#">Next</a></nav>
        <span>Answer</span>
    </div>`]]
    <a href="http://test.com" class="tooltip" title="This is my link's tooltip message!">Link</a>
    [[-<div class="tooltip div-ppost-answer-marker" title="Click to mark as answer">
        <p>Click to mark as answer</p>
    </div>]]
    <div class="dis-post-left">
        <ul>
            <li class="dis-usr-icon"><a href="[[~[[*id]]? &scheme=`full`]]u/[[+author.username]]" class="auth-avatar" title="[[%discuss.view_author_profile]]">[[+author.avatar]]</a></li>
            <li class="dis-usr-post-count">[[+author.posts]] [[%discuss.posts]]</li>
            <a href="[[~[[*id]]]]messages/new?user=[[+author.username]]" class="dis-pm-btn" href="">Send PM</a>
        </ul>
    </div>

    <div class="dis-post-right">
        <div class="title">
            <strong>[[+author.username_link]]</strong> <a class="normal-type" href="[[+url]]" title="[[%discuss.post_link]]">Reply #[[+idx]]</a>, <span title="[[+createdon]]">[[+createdon:ago]]</span>
            <!-- tools -->
            <div class="dis-actions">
                <div>
                    <ul>[[+actions]]
                        <li><a href="[[+url]]">[[%discuss.post_link]]<span class="idx">#[[+idx]]</span></a></li>
                        <li>[[+report_link]]</li>
                    </ul>
                </div>
            </div>
            <!-- /tools -->

        </div>
        <div class="dis-content">
            [[+content]]
            [[+editedby:is=`0`:then=``:else=`<span class="dis-post-editedon">Edited [[+editedon:ago]] by <a href="[[~[[*id]]]]user?user=[[+editedby]]">[[+editedby.username]]</a></span>`]]
        </div>
        <ul class="dis-action-btn">[[+report_link]][[+action_reply]]</ul>
    </div>
    [[+author.signature:notempty=`<div class="dis-signature">[[+author.signature]]</div>`]]
    <div class="dis-post-footer">
            [[+attachments:notempty=`<div class="dis-post-attachments"><ul class="dis-attachments">[[+attachments]]</ul></div>`]]
        [[-<div class="dis-post-ip">
        </div>]]
    </div>
</li>
