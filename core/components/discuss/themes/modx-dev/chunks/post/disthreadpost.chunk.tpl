<li class="[[+class]] group-fix" id="dis-post-[[+id]]" data-author="[[+author.username:htmlent]]" data-date="[[+createdon_raw]]" data-message="[[+content_raw]]">
    [[+answer:isequal=`1`:then=`
    <div class="dis-post-answer-marker">
        <nav>
            [[+answer_prev.id:notempty=`<a href="[[+answer_prev.url]]">Previous</a>`]]
            [[+answer_next.id:notempty=`<a href="[[+answer_next.url]]" class="next">Next</a>`]]
        </nav>
        [[+url_mark_as_answer:notempty=`<a href="[[+url_mark_as_answer]]"><span class="tooltip" title="Unmark as Answer">Answer</span></a>`]]
    </div>`:else=`
    [[+url_mark_as_answer:notempty=`<div class="dis-post-answer-marker dis-post-notanswer"><a href="[[+url_mark_as_answer]]" class="tooltip" title="Mark as Answer!"><span>Mark as Answer</span></a></div>`]]
    `]]
    <!-- mark answer-->
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
