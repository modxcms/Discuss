	    [[+pagination]]

<div class="dis-threads">


	
	
	<ul class="dis-list">
		<li><h1>Unread Posts</h1></li>
	[[+threads]]
	</ul>
	    [[+pagination]]
</div>


				</div><!-- Close Content From Wrapper -->
[[+bottom]]

<aside>
				<hr class="line" />
    <div class="PanelBox">

[[!+discuss.user.id:notempty=`<div class="Box">
				           <h4>Actions</h4>
				            <p>[[+actionbuttons]]</p>
				            <p>[[+readers]]</p>
							<p>[[+moderators]]</p>
				        </div>`]]
				        [[!+discuss.user.id:is=``:then=`<div class="Box">
				           <h4>Actions</h4>
				            <p><a href="[[~[[*id]]]]login" class="Button">Login to Post</a></p>
				        </div>`]]
			<div class="Box">
			<h4>Information</h4>
			<p>[[+readers]]</p>
			</div>
</aside>