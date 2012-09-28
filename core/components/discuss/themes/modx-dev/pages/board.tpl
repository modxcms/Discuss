<!-- board.tpl -->
[[+top]]
[[+aboveBoards]]
[[+boards:notempty=`
<div class="dis-threads forum-grid category boards">[[+boards]]</div>
`]]
[[+belowBoards]]
<div class="dis-threads forum-grid">
	<div class="m-section_title">
		<header class="dis-cat-header dark-gradient group-fix sticky-bar">
			<h1>[[+name]]</h1>
			[[+pagination:notempty=`
			<nav id="key-Paginate" class="paginate horiz-list">[[+pagination]]</nav>
			`]]
			[[- USER LOGGED IN ]]
	        [[!+discuss.user.id:notempty=`<div class="post-box h-group">
				[[+actionbuttons]]
				[[+moderators]]
		    </div>`]]

		    [[- USER NOT LOGGED IN ]]
		    [[!+discuss.user.id:is=``:then=`
		    <div class="post-box">
				<a href="[[~[[*id]]]]login" class="Button dis-action-login" >Login to Post</a>
			</div>
			`]]
		</header>
		<div class="row h-group header-row">
		    <div class="f1-f7 f-padinall">
		    	<div class="wrap">Title</div>
		    </div>
		    <div class="f8 f-padinall">Views</div>
		    <div class="f9 f-padinall">Replies</div>
		    <div class="f10-f12 f-padinall">Info</div>
		</div>
	</div> <!-- / m-section_title -->
	[[+posts]]
	[[+pagination:notempty=`<nav class="paginate stand-alone bottom horiz-list"> [[+pagination]]</nav>`]]
</div>
<!-- bottom -->[[+bottom]] <!-- /bottom -->
<!-- / board.tpl -->
