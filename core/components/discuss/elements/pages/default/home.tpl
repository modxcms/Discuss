[[+trail]]

<ol class="dis-board-list">
[[+boards]]
</ol>

<br class="clear" />

[[+actionbuttons]]

<br class="clear" />


<div class="dis-threads">
    <div class="dis-threads-header">
    <h2 style="margin: 0; padding: 0">[[%discuss.recent_posts? &namespace=`discuss` &topic=`web`]]</h2>
    </div>
    <ol class="dis-board-list">
        [[+recent_posts]]
    </ol>
</div>

<br class="clear" />

<ol class="dis-board-list dis-stats">
    <li class="dis-category-li"><h2>[[%discuss.stats]]</h2></li>
    <li class="dis-board-li">
        [[%discuss.stats_totals? &posts=`[[+totalPosts]]` &threads=`[[+totalTopics]]` &members=`[[+totalMembers]]`]]
    </li>
    <li class="dis-board-li">
        [[%discuss.stats_online? &visitors=`[[+totalVisitorsActive]]` &members=`[[+totalMembersActive]]`]]
        <br />
        <span class="dis-active-users dis-small">
        [[+activeUsers]]
        </span>
    </li>
</ol>