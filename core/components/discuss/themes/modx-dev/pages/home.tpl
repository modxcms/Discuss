<!-- home.tpl -->
[[+top]]
[[+aboveBoards]]
	    [[+pagination]]
<div class="dis-threads f-all">

	<div class="dis-threads forum-grid category panel-stack">
	[[+boards]]
	</div>
[[+belowBoards]]
[[+aboveRecent]]
</div>
[[+belowRecent]]
	    [[+pagination]]
[[+threadactionbuttons]]
				</div><!-- Close Content From Wrapper -->
			[[+bottom]]
				[[-
				<!-- Removing sidebar on views need the maximum width possible
				<aside>
				<hr class="line" />
					<div class="PanelBox">
						<div class="Box">
						   <h4>Other Support Optionss modx-dev</h4>
							<p>Tso file a bug or make a feature request <a href="http://bugs.modx.com">visit our issue tracker</a>, or you can also <a href="[[~10]]" title="MODX Direct Commercial Support">purchase commercial support</a>.</p>
						</div>
						<div class="Box">
						   <h4>Love MODX?</h4>
							<p>If you build sites with MODX or just love using it, why not <a href="http://modx.com/community/wall-of-fame/support-modx/">give back</a>?</p>
						</div>
						<div class="Box">
						   <h4>[[%discuss.stats]]</h4>
							<p class="stats">[[%discuss.stats_totals? &posts=`[[+totalPosts]]` &threads=`[[+totalTopics]]` &members=`[[+totalMembers]]`]]</p>
							<p class="stats">[[%discuss.stats_online? &visitors=`[[+totalVisitorsActive]]` &members=`[[+totalMembersActive]]`]]</p>
							<p class="stats">[[+activeUsers]]</p>
							<p class="stats">[[%discuss.stats_today? &hits=`[[+activity.hits]]` &topics=`[[+activity.topics]]` &replies=`[[+activity.replies]]` &visitors=`[[+activity.visitors]]`]]</p>
						</div>
					</aside> -->
				]]