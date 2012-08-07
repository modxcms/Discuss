<!-- board.tpl -->
[[+top]]


[[+aboveBoards]]


	    <!--[[+pagination]]-->
[[-<!--<ul class="dis-list" style="[[+boards_toggle]]">-->]]
	[[+boards:notempty=`<div class="dis-threads forum-grid category boards">
[[+boards]]
	</div>`]]

[[+belowBoards]]

<div class="dis-threads forum-grid">
	<div class="m-section_title">
	<header class="dis-cat-header dark-gradient group-fix sticky-bar">
		<h1>[[+name]]</h1>
		<nav id="key-Paginate" class="paginate horiz-list">[[+pagination]]</nav>
		[[- USER LOGGED IN ]]
        [[!+discuss.user.id:notempty=`<div class="post-box">
			<p>[[+actionbuttons]]</p>
			<p>[[+moderators]]</p>
	    </div>`]]

	    [[- USER NOT LOGGED IN ]]
	    [[!+discuss.user.id:is=``:then=`<div class="post-box">
			<p><a href="[[~[[*id]]]]login" class="Button">Login to Post</a></p>
		</div>`]]
	</header>
	<div class="row h-group header-row">
	    <div class="f1-f7 f-padinall"><div class="wrap">Title</div></div>
	    <div class="f8 f-padinall">Views</div>
	    <div class="f9 f-padinall">Replies</div>
	    <div class="f10-f12 f-padinall">Info</div>
	</div>
	</div>

	[[+posts]]


	   <nav class="paginate stand-alone bottom horiz-list"> [[+pagination]]</nav>
</div>


</div><!-- Close Content From Wrapper -->

[[+bottom]]


[[-
<!-- <aside>
				<hr class="line" />
    <div class="PanelBox">
        [[!+discuss.user.id:notempty=`<div class="Box">
            <h4>Actions</h4>
			<p>[[+actionbuttons]]</p>
			<p>[[+moderators]]</p>
	    </div>`]]
        [[!+discuss.user.id:is=``:then=`<div class="Box">
		    <h4>Actions</h4>
			<p><a href="[[~[[*id]]]]login" class="Button">Login to Post</a></p>
		</div>`]]

		[[!$post-sidebar?disection=`dis-support-opt`]]


    </aside>
 -->]]