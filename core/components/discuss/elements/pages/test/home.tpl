<div id="board-wrapper">
	<ul class="breadcrumbs">
		<li>[[++discuss.forum_title]]</li>
	</ul>	
	
	<hr class="clear spacer"/>
	
	[[+actionbuttons:notempty=`
		<ul class="actions right">
			[[+actionbuttons]]
		</ul>
	`]]
	
	<hr class="clear spacer"/>
	<div class="categories left">
		[[+boards]]	
		
		[[+actionbuttons:notempty=`
			<ul class="actions right">
				[[+actionbuttons]]
			</ul>
		`]]
		
		<hr class="clear spacer"/>
		
		<div class="forum_stats">
			<h3 class="category_title">[[%discuss.stats]]</h3>
			<ol class="list">
				<li>
					[[%discuss.stats_totals? &posts=`[[+totalPosts]]` &threads=`[[+totalTopics]]` &members=`[[+totalMembers]]`]]
					<br />
					[[%discuss.stats_latest_post?
						&post=`<a class="topic" href="[[~[[++discuss.thread_resource]]? &thread=`[[+latestPost.thread]]`]]#dis-board-post-[[+latestPost.id]]">[[+latestPost.title]]</a>`
						&by=`<a href="[[~[[++discuss.user_resource]]? &user=`[[+latestPost.author]]`]]">[[+latestPost.username]]</a>`
					]]
				</li>
				<li>
					[[%discuss.stats_online? &visitors=`[[+totalVisitorsActive]]` &members=`[[+totalMembersActive]]`]]
					<br />
					<span class="active_users">
					[[+activeUsers]]
					</span>
				</li>
			</ol>
		</div>
		<!-- End .forum_stats -->
	
	</div>
	<!-- End .categories -->
	
	<div id="sidebar">
	
		[[+discuss.loginForm]]
		
		<div class="forum_recent">
			<h3 class="category_title">[[%discuss.recent_posts? &namespace=`discuss` &topic=`web`]]</h3>
			<ol class="list">
				[[!DiscussRecentPosts]]
			</ol>
		</div>		
		
	</div>
	<hr class="clear"/>
</div>