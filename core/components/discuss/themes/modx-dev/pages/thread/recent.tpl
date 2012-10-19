<!-- recent.tpl -->
<div class="f1-f9">
	<div class="dis-threads forum-grid">
		<div class="m-section_title">
			<header class="dis-cat-header dark-gradient group-fix sticky-bar">
				<h1>[[+discuss.pagetitle]]</h1>
				[[+pagination:notempty=`
				<nav id="key-Paginate" class="horiz-list">[[+pagination]]</nav>
				`]]
			</header>
			<div class="row h-group header-row">
			    <div class="f1-f2 f-padinall">
			    	<div class="wrap">[[%discuss.board]]</div>
			    </div>
			    <div class="f3-f6">[[%discuss.post]]</div>
                <div class="f7">[[%discuss.author]]</div>
                <div class="f8">[[%discuss.posted_on]]</div>
			    <div class="f9">[[%discuss.thread]] [[%discuss.replies]]</div>
			</div>
		</div> <!-- / m-section_title -->
		[[+recent_posts]]
		[[+pagination:notempty=`<nav class="paginate stand-alone bottom horiz-list">[[+pagination]]</nav>`]]
	</div>
</div>

[[+bottom]]

[[+sidebar]]
