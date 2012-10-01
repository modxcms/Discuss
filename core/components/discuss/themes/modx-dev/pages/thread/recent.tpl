<!-- recent.tpl -->
	<div class="f1-f9">
		<nav id="key-Paginate" class="paginate stand-alone horiz-list">[[+pagination]]</nav>
		<div class="dis-threads">
			<ul class="dis-list">
				<li><h1>Recent Posts</h1></li>
				[[+recent_posts]]
			</ul>
			<nav id="key-Paginate" class="paginate stand-alone horiz-list bottom">[[+pagination]]</nav>
		</div>
	</div>

	<aside class="f10-f12">
		<div class="PanelBox">
		[[!$post-sidebar?disection=`new-message`]]
	</aside>

</div><!-- Close Content From Wrapper -->
[[+bottom]]


