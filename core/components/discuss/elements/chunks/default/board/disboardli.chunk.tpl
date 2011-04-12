<li class="dis-board-li dis-category-[[+category]] [[+unread-cls]]">
    <div class="right dis-board-li-last-post">[[+lastPost]]</div>
    <div class="right dis-board-li-stats">
    [[%discuss.board_post_stats? &posts=`[[+total_posts]]` &topics=`[[+num_topics]]` &unread=`[[+unread]]`]]
    </div>

    <h3><a href="[[~[[*id]]]]board/?board=[[+id]]">[[+name]]</a></h3>
    <p>[[+description]]</p>
    <span class="dis-board-subs">[[+subforums:notempty=`Sub-Forums: [[+subforums]]`]]</span>
</li>