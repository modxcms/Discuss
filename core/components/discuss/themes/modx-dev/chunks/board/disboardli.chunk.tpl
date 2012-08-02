<!-- chunk/boards/disboardli.chunk.tpl -->
<div class="Depth2 row dis-category-[[+category]] read-status-[[+unread-cls]]">
    <div class="first cell">
        <a href="[[+url]]"><strong>[[+name]]</strong></a>
        <p class="dis-board-description">[[+description]]</p>
    </div>
    <div class="second cell">Title of Last Post</div>
    <div class="third cell">[[+post_stats]]</div>
    [[+subforums:notempty=`<p class="dis-board-subs [[+unread-cls]] group-fix"><strong>Subtopics:</strong> [[+subforums]]</p>`]]
</div>
[[-
<!--<li class="Depth2  dis-category-[[+category]]">
    <div class="ItemContent">
    	<a href="[[+url]]" class="dis-cat-links [[+unread-cls]]">
    		<h3>
    		<span class="Title">[[+name]] <span class="DiscussionCount">[[+post_stats]]</span></span>
    		</h3>
	    	<p class="CategoryDescription">[[+description]]</p>
	    </a>
			[[+subforums:notempty=`<p class="dis-board-subs [[+unread-cls]]"><strong>Subtopics:</strong> [[+subforums]]</p>`]]
	</div>
</li>-->
]]
