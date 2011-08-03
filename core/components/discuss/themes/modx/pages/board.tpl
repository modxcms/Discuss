[[+top]]

[[+aboveBoards]]
<ol class="dis-board-list" style="[[+boards_toggle]]">
[[+boards]]
</ol>

[[+belowBoards]]

<div class="dis-threads">

	<ul class="DataList CategoryList CategoryListWithHeadings">
		<li class="Item CategoryHeading Depth1">
	    <div class="ItemContent Category">[[+name]]</div>
	    </li>
	</ul>

	<ol class="dis-board-thread">
		[[+posts]]
	</ol>

    <div class="dis-pagination"><ul>[[+pagination]]</ul></div>
</div>

<div style="padding:10px;">
    <p class="dis-thread-viewing clear">[[+readers]]</p>
    <p class="dis-moderators">[[+moderators]]</p>
</div>



</div><!-- Close Content From Wrapper -->

[[+bottom]]



<div id="Panel">
    <div class="PanelBox">

        <div class="Box GuestBox">
        <h4>Search Board</h4>
        <form action="[[~[[*id]]]]search" method="GET">
            <input type="hidden" name="board" value="[[+id]]" />
            <label><input type="text" name="s" value="" placeholder="[[%discuss.search_this_board]]" /></label>

            <input type="submit" class="dis-action-btn-solo"  value="[[%discuss.search]]" />
        </form>
        </div>

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
