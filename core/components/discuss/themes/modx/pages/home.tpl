[[+top]]
[[+aboveBoards]]

<div class="dis-threads">

<ul class="DataList CategoryList CategoryListWithHeadings">
	[[+boards]]
</ul>

[[+belowBoards]]


[[+aboveRecent]]



	<ul class="DataList CategoryList CategoryListWithHeadings">
	
		<li class="Item CategoryHeading Depth1 Category-[[%discuss.recent_posts? &namespace=`discuss` &topic=`web`]]">
	    <div class="ItemContent Category Read">[[%discuss.recent_posts? &namespace=`discuss` &topic=`web`]]</div>
	    </li>
	
	        [[+recent_posts]]
	
	</ul>
</div>


[[+belowRecent]]


<ul class="CategoryList CategoryListWithHeadings">
		<li class="Item CategoryHeading Depth1">
	    <div class="ItemContent Category Read">[[%discuss.stats]]</div>
	    </li>
    <li class="Item Depth2  dis-category-1">
        [[%discuss.stats_totals? &posts=`[[+totalPosts]]` &threads=`[[+totalTopics]]` &members=`[[+totalMembers]]`]]
    </li>
    <li class="Item Depth2  dis-category-1">
        [[%discuss.stats_online? &visitors=`[[+totalVisitorsActive]]` &members=`[[+totalMembersActive]]`]]
        <br />
        <span class="dis-active-users dis-small">
        [[+activeUsers]]
        </span>
    </li>
    <li class="Item Depth2  dis-category-1">
        <span class="dis-today-stats">[[%discuss.stats_today? &hits=`[[+activity.hits]]` &topics=`[[+activity.topics]]` &replies=`[[+activity.replies]]` &visitors=`[[+activity.visitors]]`]]</span>
    </li>
</ul>

							<div class="dis-pagination"><ul>[[+pagination]]</ul></div>

[[+threadactionbuttons]]

				</div><!-- Close Content From Wrapper -->
			[[+bottom]]

				<div id="Panel">
					<div class="PanelBox">
					
						<div class="Box GuestBox">
						  	<h4>Subscribe to Feed</h4>
						  	<p><a href="[[~[[*id]]]]thread/recent.xml" class="rss_feed">RSS Feed</a></p>
						</div>
					
						<div class="Box GuestBox">
						   <h4>Other Support Options</h4>
							<p>To file a bug or make a feature request <a href="http://bugs.modx.com">visit our issue tracker</a>.</p>
						</div>
						
						<div class="Box GuestBox">
						   <h4>Want to Support MODX?</h4>
							<p>If you build sites for a living with MODX, why not <a href="http://modx.com/community/wall-of-fame/support-modx/">give back</a>?</p>
						</div>
					</div>

