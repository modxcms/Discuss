<div class="row h-group dis-category-[[+category]] [[+class]] [[+locked:is=`1`:then=`locked`:else=`unlocked`]] [[+unreadCls]]">
   <a class="h-group" href="[[+url]]">
   		<div class="f1-f2">
   			[[+board_name]]
   		</div>
        <div class="f3-f7 m-title">
            <div class="wrap">
                [[+answered:notempty=`<span class="answered">solved</span>`]]
                <a class="h-group" href="[[+url]]"><strong>[[+sticky:if=`[[+sticky]]`:eq=`1`:then=`[[+title]]`:else=`[[+title]]`]]</strong></a>
                [[+thread_pagination]]
            </div>
        </div>
        <div class="f8">[[+views]]</div>
        <div class="f9">[[+replies]]</div>
        <div class="f10-f12">
            <p class="posted-date">[[+createdon:ago]]</p>
            <p class="posted-by">[[+first_post_username]]</p>
        </div>
    </a>
</div>

[[-
<li class="Depth2 [[+class]]">
    <div class="ItemContent">
      <a href="[[+url]]" class="dis-cat-links [[+unread-cls]]">
        <h3 class="[[+locked:is=`1`:then=`locked`:else=`unlocked`]]"><span class="dis-post-board-name">[[+board_name]]</span>
        <span class="Title">[[+sticky:eq=`1`:then=`<strong>[[+title]]</strong>`:else=`[[+title]]`]]</span><br />
        </h3>
        <p class="CategoryDescription">[[+first_post_username:notempty=`Started by [[+first_post_username]] - `]] Last post by [[+author_username]] [[+createdon:ago]] - [[+replies]] replies</p>
      </a>
  </div>
</li>
]]