<p class="dis-breadcrumbs">[[++discuss.forum_title]]</p>

<ol class="dis-board-list">
[[+boards]]
</ol>

<br class="clear" />

[[+discuss.loginForm]]

[[+actionbuttons]]

<br class="clear" />

<ol class="dis-board-list">
    <li class="dis-category-li"><h2>[[%discuss.recent_posts? &namespace=`discuss` &topic=`web`]]</h2></li>
    [[+recentPosts]]
</ol>

<br class="clear" />


<ol class="dis-board-list">
    <li class="dis-category-li"><h2>Stats</h2></li>
    <li class="dis-board-li" style="background: none; padding: 5px 50px;">
        [[+totalPosts]] posts in [[+totalTopics]] topics by [[+totalMembers]] members.
        <br />
        Latest Post: <a href="[[~[[++discuss.thread_resource]]]]?thread=[[+latestPost.thread]]#dis-board-post-[[+latestPost.id]]">[[+latestPost.title]]</a>
        by <a href="[[~[[++discuss.user_resource]]]]?user=[[+latestPost.author]]">[[+latestPost.username]]</a>
    </li>
    <li class="dis-board-li" style="background: none; padding: 5px 50px;">
        [[+totalVisitorsActive]] Visitors, [[+totalMembersActive]] Members
        <br />
        <span class="dis-active-users dis-small" style="font-size: 10px;">
        [[+activeUsers]]
        </span>
    </li>
</ol>