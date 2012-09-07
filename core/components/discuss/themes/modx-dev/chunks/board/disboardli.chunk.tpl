<!-- chunk/boards/disboardli.chunk.tpl -->
<div class="Depth2 row dis-category h-group dis-category-[[+category]] [[+unread-cls]]">
    <a href="[[+url]]" class="h-group">
        <div class="f1-f7">
            <div class="wrap">
                <strong>[[+name]]</strong>
                <p class="dis-board-description">[[+description]]</p>
            </div>
        </div>
        <div class="f8-f10">[[+last_post_title]]</div>
        <div class="f11-f12">[[+num_replies]] posts, [[+num_topics]] topics</div>
    </a>
    [[+subforums:notempty=`<div class="h-group f-all"><p class="dis-board-subs [[+unread-cls]]">[[-<strong>Subtopics:</strong>]] [[+subforums]]</p></div>`]]
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
