[[+pagination:default=``]]
<!-- recent.tpl -->
<div class="f1-f9">
	<div class="dis-threads forum-grid">
		<div class="m-section_title">
			<header class="dis-cat-header dark-gradient group-fix sticky-bar">
				<h1>Private Messages</h1>
				[[+pagination:notempty=`
				<nav id="key-Paginate" class="horiz-list right">[[+pagination]]</nav>
				`]]
				[[!+discuss.user.id:notempty=`<div class="post-box h-group">[[+actionbuttons]]</div>`]]
			</header>
			<div class="row h-group header-row">
			    <div class="f1-f2 f-padinall">
			    	<div class="wrap">From</div>
			    </div>
			    <div class="f3-f6 f-padinall">[[%discuss.title]]</div>
			    <div class="f7 f-padinall">[[%discuss.last_post]]</div>
			    <div class="f8 f-padinall">Created</div>
			    <div class="f9 f-padinall">[[%discuss.replies]]</div>
			</div>
		</div> <!-- / m-section_title -->
		[[+messages]]
		[[+pagination:notempty=`<nav class="paginate stand-alone bottom horiz-list"> [[+pagination]]</nav>`]]
	</div>
</div>

[[+bottom]]

[[+sidebar]]
