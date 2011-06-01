[[+top]]
[[+trail]]

[[+aboveBoards]]

<ul class="DataList CategoryList CategoryListWithHeadings">
	[[+boards]]
</ul>

[[+belowBoards]]

<br class="clear" />

[[+actionbuttons]]

<br class="clear" />

[[+aboveRecent]]

<div class="dis-threads">
    <div class="dis-threads-header">
    <h2 style="margin: 0; padding: 0">[[%discuss.recent_posts? &namespace=`discuss` &topic=`web`]]</h2>
    </div>
    <ol class="dis-board-list">
        [[+recent_posts]]
    </ol>
</div>

[[+belowRecent]]

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
    <li class="dis-board-li">
        <span class="dis-today-stats">[[%discuss.stats_today? &hits=`[[+activity.hits]]` &topics=`[[+activity.topics]]` &replies=`[[+activity.replies]]` &visitors=`[[+activity.visitors]]`]]</span>
    </li>
</ol>

[[+bottom]]