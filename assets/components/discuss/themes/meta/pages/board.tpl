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

	<ul class="DataList CategoryList CategoryListWithHeadings">
		<li class="Item CategoryHeading Depth1">
	    <div class="ItemContent Category">[[%discuss.message]]</div>
	    </li>
	</ul>

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
						

						
					</div>
