<!-- recent.tpl -->
<div class="f1-f9">
	<div class="dis-threads forum-grid">
		<div class="m-section_title">
			<header class="dis-cat-header dark-gradient group-fix sticky-bar">
				<h1>Recent Posts</h1>
				[[+pagination:notempty=`
				<nav id="key-Paginate" class="paginate horiz-list">[[+pagination]]</nav>
				`]]
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
		[[+recent_posts]]
		[[+pagination:notempty=`<nav class="paginate stand-alone bottom horiz-list">[[+pagination]]</nav>`]]
	</div>
</div><!-- Close Content From Wrapper -->

[[+bottom]]

[[$post-sidebar?dissection=`recent`]]

<!-- end thread/recent.tpl -->
