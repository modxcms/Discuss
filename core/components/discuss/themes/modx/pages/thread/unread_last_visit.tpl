
<div class="dis-threads">

	<h1 class="Category">Unread Posts Since Last Visit</h1>

	<ul class="DataList CategoryList CategoryListWithHeadings">

		[[+threads]]
	</ul>

[[+pagination]]
</div>
				</div><!-- Close Content From Wrapper -->
[[+bottom]]

<div id="Panel">
				<hr class="line" />
    <div class="PanelBox">
[[!+discuss.user.id:notempty=`<div class="Box GuestBox">
				           <h4>Actions &amp; Info</h4>
				            <p>[[+actionbuttons]]</p>
				            [[+belowThreads]]
				            <p>[[+readers]]</p>
							<p>[[+moderators]]</p>
				        </div>`]]
				        [[!+discuss.user.id:is=``:then=`<div class="Box GuestBox">
				           <h4>Actions &amp; Info</h4>
				            <p><a href="[[~[[*id]]]]login" class="Button">Login to Post</a></p>
				        </div>`]]


    </div>