<li class="[[+class]] group-fix" id="dis-post-[[+id]]" data-author="[[+author.username:htmlent]]" data-date="[[+createdon_raw]]" data-message="[[+content_raw]]">
    [[+answer:neq=``:then=`
    <div class="dis-post-answer-marker">
        [[+answer_count:gt=`1`:then=`
            <nav>
            [[+answer_prev.link]]
            [[+answer_next.link]]
            </nav>
        `:else=``]]

        [[+url_mark_as_answer:eq=``:then=`
            <span title="[[%discuss.answer]]">[[%discuss.answer]]</span>
        `:else=`
        <a href="[[+url_mark_as_answer]]">
            <span title="[[%discuss.unflag_answer]]">[[%discuss.unflag_answer]]</span>
        </a>
        `]]
    </div>
    `:else=`
        <div class="dis-post-answer-marker dis-post-notanswer">
            [[+url_mark_as_answer:eq=``:then=``:else=`
            <div class="dis-post-answer-marker dis-post-notanswer">
                <p>[[%discuss.flag_answer]]</p>
                <a href="[[+url_mark_as_answer]]">
                    <span>[[%discuss.flag_answer]]</span>
                </a>
            </div>
            `]]
        </div>
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
            [[+action_modify:notempty=`<ul class="dis-content-actions">[[+action_modify]][[+action_remove]][[+action_spam]]</ul>`]]
        </div>
        <div class="dis-content">
            [[+content]]
            [[+discuss.user.shouldMarkAnAnswer:eq=`1`:then=`
                [[+idx:eq=`1`:then=`
                    <div class="dis-info"><p>[[%discuss.mark_answer_instructions]]</p></div>
                `:else=``]]
            `:else=``]]
            [[+idx:eq=`1`:then=`
                <div class="dis-info"><p>[[+jump_to_first_answer.link]]</p></div>
            `]]
            [[+editedby:is=`0`:then=``:else=`<span class="dis-post-editedon">[[%discuss.editedon_post? &on=`[[+editedon:ago]]` &user=`[[+editedby.username]]`]]</span>`]]
        </div>
        <ul class="dis-action-btn">[[+report_link]][[+action_reply]]</ul>
    </div>
    [[+author.signature:notempty=`<div class="dis-signature">[[+author.signature]]</div>`]]
    <div class="dis-post-footer">
        [[+attachments:notempty=`<div class="dis-post-attachments"><ul class="dis-attachments">[[+attachments]]</ul></div>`]]
    </div>
</li>
