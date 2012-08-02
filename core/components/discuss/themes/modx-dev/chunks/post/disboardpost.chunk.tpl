<!-- REMOVEPRODUCTION post/disboardpost.chunk.tpl -->
[[-<!--<li class="Depth2  dis-category-[[+category]]">
    <div class="ItemContent">
    	<a href="[[+url]]" class="dis-cat-links [[+unreadCls]]">
    		<h3 class="[[+locked:is=`1`:then=`locked`:else=`unlocked`]]">
    			<span class="Title">[[+sticky:if=`[[+sticky]]`:eq=`1`:then=`<strong>[[+title]]</strong>`:else=`[[+title]]`]] <span class="DiscussionCount">[[+replies]] replies, [[+views]] views</span>
    		</h3>
    		<p class="CategoryDescription">Started by [[+first_post_username]], last post by [[+last_post_username]] [[+createdon:ago]]</p>
	    </a>
	</div>
</li>-->]]
<div class="row dis-category-[[+category]] [[+locked:is=`1`:then=`locked`:else=`unlocked`]]">
    <div class="first cell"><a href="[[+url]]"><strong>[[+sticky:if=`[[+sticky]]`:eq=`1`:then=`[[+title]]`:else=`[[+title]]`]]</strong></a>
    </div>
    <div class="second cell">[[+views]]</div>
    <div class="third cell">[[+replies]]</div>
    <div class="fourth cell">
        <p class="posted-date">[[+createdon:ago]]</p>
        <p class="posted-by">[[+first_post_username]]</p>
    </div>
</div>