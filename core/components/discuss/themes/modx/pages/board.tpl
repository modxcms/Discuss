[[+top]]
[[<div class="right">
    <form action="[[~[[*id]]]]search" method="GET">
        <input type="hidden" name="board" value="[[+id]]" />
        <input type="text" name="s" value="" class="dis-form-field-solo" style="width: 200px; margin-right: 5px;" placeholder="[[%discuss.search_this_board]]" />

        <input type="submit" class="dis-action-btn-solo"  value="[[%discuss.search]]" />
    </form>
</div>]]
[[+trail]]

[[+aboveBoards]]
<ol class="dis-board-list" style="[[+boards_toggle]]">
[[+boards]]
</ol>

[[+belowBoards]]

<div class="dis-threads">

    <div class="dis-board-head">
        <div class="dis-thread-body"><h3>[[%discuss.message]]</h3></div>
        <div class="dis-views"><h3>[[%discuss.views]]</h3></div>
        <div class="dis-replies"><h3>[[%discuss.replies]]</h3></div>
        <div class="dis-latest"><h3>[[%discuss.last_post]]</h3></div>
    </div>

	<ol class="dis-board-thread">
		[[+posts]]
	</ol>
	
</div>

<br class="clear" />



<div class="dis-pagination"><ul>[[+pagination]]</ul></div>

<div class="dis-thread-actions">[[+actionbuttons]]</div>

[[+belowThreads]]

<div class="dis-viewing">[[+readers]]</div>
<div class="dis-viewing">[[+moderators]]</div>


				</div><!-- Close Content From Wrapper -->

[[+bottom]]



				<div id="Panel">
					<div class="PanelBox">
					
						<div class="Box GuestBox">
						   <h4>Welcome back [[+modx.user.username]]</h4>
							<p>Some information. Test.</p>
						</div>
						
						<div class="Box BoxCategories">
							<h4><a href="/categories/all">Categories</a></h4>
							<ul class="PanelInfo PanelCategories">
								<li><span><strong><a href="/discussions">All Discussions</a></strong><span class="Count">[[+totalPosts]]</span></span></li>
								<li class="Heading">Partners</li>
								<li class="Depth2 active"><strong><a href="/categories/-important-news">Sample 1 Active</a></strong><span class="Count">0</span></li>
								<li class="Depth2"><strong><a href="/categories/security-notices">Sample 2</a></strong><span class="Count">0</span></li>
								<li class="Heading">General</li>
								<li class="Depth2 active"><strong><a href="/categories/-important-news">Sample 1 Active</a></strong><span class="Count">0</span></li>
								<li class="Depth2"><strong><a href="/categories/security-notices">Sample 2</a></strong><span class="Count">0</span></li>
								<li class="Heading">Evolution</li>
								<li class="Depth2 active"><strong><a href="/categories/-important-news">Sample 1 Active</a></strong><span class="Count">0</span></li>
								<li class="Depth2"><strong><a href="/categories/security-notices">Sample 2</a></strong><span class="Count">0</span></li>
								<li class="Heading">Revolution</li>
								<li class="Depth2 active"><strong><a href="/categories/-important-news">Sample 1 Active</a></strong><span class="Count">0</span></li>
								<li class="Depth2"><strong><a href="/categories/security-notices">Sample 2</a></strong><span class="Count">0</span></li>
								<li class="Heading">International</li>
								<li class="Depth2 active"><strong><a href="/categories/-important-news">Sample 1 Active</a></strong><span class="Count">0</span></li>
								<li class="Depth2"><strong><a href="/categories/security-notices">Sample 2</a></strong><span class="Count">0</span></li>
							</ul>
						</div>
					</div>
