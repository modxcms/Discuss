<div class="f1-f9">
	<div class="dis-threads forum-grid">
		<div class="m-section_title">
			<header class="dis-cat-header dark-gradient group-fix sticky-bar">
				<h1>[[+discuss.pagetitle]]</h1>
				[[+pagination:notempty=`<nav id="key-Paginate" class="horiz-list">[[+pagination]]</nav>`]]
                <div class="post-box h-group">
                    <a href="[[~[[++discuss.forums_resource_id]]]]thread/unread" class="action-buttons dis-action-unread_last_visit" title="[[%discuss.unread_posts_all]]">[[%discuss.unread_posts_all]]</a>
		            <a class="read" href="[[+actionlink_unread]]" title="[[%discuss.mark_all_as_read]]">[[%discuss.mark_all_as_read]]</a>
                </div>

			</header>
			<div class="row h-group header-row">
			    <div class="f1-f2 f-padinall">
			    	<div class="wrap">[[%discuss.board]]</div>
			    </div>
			    <div class="f3-f6 f-padinall">[[%discuss.thread]]</div>
                <div class="f7 f-padinall">[[%discuss.author]]</div>
                <div class="f8 f-padinall">[[%discuss.posted_on]]</div>
			    <div class="f9 f-padinall">[[%discuss.replies]]</div>
			</div>
		</div> <!-- / m-section_title -->
		[[+threads]]
		[[+pagination:notempty=`<nav class="paginate stand-alone bottom horiz-list">[[+pagination]]</nav>`]]
	</div>
</div>

[[+sidebar]]
