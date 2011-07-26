[[+top]]

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
						[[+discuss.user.id:notempty=`<div class="Box GuestBox">
						   <h4>Actions &amp; Info</h4>
							<p>[[+actionbuttons]]</p>
						</div>`]]
						
						<div class="Box GuestBox">
						   <h4>Other Support Options</h4>
							<p>To file a bug or make a feature request <a href="http://bugs.modx.com">visit our issue tracker</a>.</p>
						</div>
						
						<div class="Box GuestBox">
						   <h4>Want to Support MODX?</h4>
							<p>If you build sites for a living with MODX, why not <a href="http://modx.com/community/wall-of-fame/support-modx/">give back</a>?</p>
						</div>
						
					</div>
