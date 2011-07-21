[[+top]]
[[+trail]]
[[<div class="right">
    <form action="[[~[[*id]]]]search" method="GET">
        <input type="hidden" name="board" value="[[+id]]" />
        <input type="text" name="s" value="" class="dis-form-field-solo" style="width: 200px; margin-right: 5px;" placeholder="[[%discuss.search_this_board]]" />

        <input type="submit" class="dis-action-btn-solo"  value="[[%discuss.search]]" />
    </form>
</div>]]

[[+aboveBoards]]
<ol class="dis-board-list" style="[[+boards_toggle]]">
[[+boards]]
</ol>

[[+belowBoards]]

<div class="dis-threads">

    <div class="dis-board-head">
        <div class="dis-thread-body"><h3>[[%discuss.message]]</h3></div>
       [[ <div class="dis-views"><h3>[[%discuss.views]]</h3></div>
        <div class="dis-replies"><h3>[[%discuss.replies]]</h3></div>
        <div class="dis-latest"><h3>[[%discuss.last_post]]</h3></div>]]
    </div>

	<ol class="dis-board-thread">
		[[+posts]]
	</ol>

						   <div class="dis-pagination"><ul>[[+pagination]]</ul></div>

</div>



				</div><!-- Close Content From Wrapper -->

[[+bottom]]



				<div id="Panel">
					<div class="PanelBox">
					
						<div class="Box GuestBox">
						   <h4>Welcome back [[+modx.user.username]]</h4>


							<p>[[+actionbuttons]]</p>

							[[+belowThreads]]

							<p>[[+readers]]</p>
							<p>[[+moderators]]</p>
						</div>
					</div>
