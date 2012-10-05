<div class="row h-group dis-category-[[+category]] [[+class]] [[+locked:is=`1`:then=`locked`:else=`unlocked`]] [[+unread-cls]]">
   <a class="h-group" href="[[~[[*id]]]]messages/view?thread=[[+id]]#dis-board-post-[[+post_last]]">
   		<div class="f1-f2">
   			[[+first_author_username]]
   		</div>
        <div class="f3-f6 m-title">
            <div class="wrap">
                <strong>[[+title]]</strong>
            </div>
        </div>
        <div class="f7">
            [[+last_author_username]], [[+last_post_createdon:ago]]
        </div>
        <div class="f8">
            [[+first_post_createdon:ago]]
        </div>
        <div class="f9">
            [[+replies]]
        </div>
    </a>
</div>


[[<li class="Depth2  dis-category-[[+category]]">
    <div class="ItemContent">
    	<a href="[[~[[*id]]]]messages/view?thread=[[+thread]]#dis-board-post-[[+post_id]]" class="dis-cat-links [[+unread-cls]]">
    		<h3>
    			<span class="Title">[[+title]]</span><br /><span class="DiscussionCount">[[+createdon:ago]], [[+replies]] replies</span>
    		</h3>
	    	<p class="CategoryDescription">[[+description]]</p>
	    </a>
	</div>
</li>
]]
