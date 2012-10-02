	    [[+pagination:notempty=`[[+pagination]]`]]





[[- <!-- old
<div class="dis-threads">
	<ul class="dis-list">
		<li><h1>Private Messages</h1></li>
		[[+messages]]
	</ul>
</div>
	    [[+pagination:notempty=`[[+pagination]]`]]

				</div>Close Content From Wrapper
[[+bottom]]


<aside>
				<hr class="line" />
    <div class="PanelBox">

        [[!+discuss.user.id:notempty=`<div class="Box">
            <h4>Actions</h4>
			<p>[[+actionbuttons]]</p>
			[[+belowThreads]]
	    </div>`]]
		[[!$post-sidebar?disection=`dis-support-opt`]]


</aside> -->

]]


 [[!+discuss.user.id:notempty=`<div class="post-box h-group">
					[[+actionbuttons]]
					[[+moderators]]
			    </div>`]]





<!-- recent.tpl -->
<div class="f1-f9">
	<div class="dis-threads forum-grid">
		<div class="m-section_title">
			<header class="dis-cat-header dark-gradient group-fix sticky-bar">
				<h1>Private Messages</h1>
				[[+pagination:notempty=`
				<nav id="key-Paginate" class="paginate horiz-list right">[[+pagination]]</nav>
				`]]
				<div class="post-box h-group">[[+actionbuttons]]</div>
			</header>
			<div class="row h-group header-row">
			    <div class="f1-f2 f-padinall">
			    	<div class="wrap">Board</div>
			    </div>
			    <div class="f3-f5 f-padinall">Post</div>
			    <div class="f6 f-padinall">Last post by</div>
			    <div class="f7 f-padinall">Created</div>
			    <div class="f8-f9 f-padinall">Author</div>
			</div>
		</div> <!-- / m-section_title -->
		[[+messages]]
		[[+pagination:notempty=`<nav class="paginate stand-alone bottom horiz-list"> [[+pagination]]</nav>`]]
	</div>
</div>
	<aside class="f10-f12">
		<div class="PanelBox">
		[[!$post-sidebar?disection=`new-message`]]
	</aside>

</div><!-- Close Content From Wrapper -->
[[+bottom]]