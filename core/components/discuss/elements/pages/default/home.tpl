[[+trail]]

<ol class="dis-board-list">
[[+boards]]
</ol>

<br class="clear" />

[[!Login]]

[[+actionbuttons]]

<br class="clear" />

<ol class="dis-board-list">
    <li class="dis-category-li"><h2>[[%discuss.recent_posts? &namespace=`discuss` &topic=`web`]]</h2></li>
    [[+recent_posts]]
</ol>

<br class="clear" />

<ol class="dis-board-list dis-stats">
    <li class="dis-category-li"><h2>[[%discuss.stats]]</h2></li>
    <li class="dis-board-li">
        [[%discuss.stats_totals? &posts=`[[+totalPosts]]` &threads=`[[+totalTopics]]` &members=`[[+totalMembers]]`]]
        <br />
        [[%discuss.stats_latest_post?
            &post=`<a href="[[~[[*id]]]]thread?thread=[[+latestPost.thread]]#dis-board-post-[[+latestPost.id]]">[[+latestPost.title]]</a>`
            &by=`<a href="[[~[[*id]]]]user?user=[[+latestPost.author]]">[[+latestPost.username]]</a>`
        ]]
    </li>
    <li class="dis-board-li">
        [[%discuss.stats_online? &visitors=`[[+totalVisitorsActive]]` &members=`[[+totalMembersActive]]`]]
        <br />
        <span class="dis-active-users dis-small">
        [[+activeUsers]]
        </span>
    </li>
</ol>