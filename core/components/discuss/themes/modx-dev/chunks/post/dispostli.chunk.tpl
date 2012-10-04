<div class="row h-group dis-category-[[+category]] [[+class]] [[+locked:is=`1`:then=`locked`:else=`unlocked`]] [[+unreadCls]]">
   <a class="h-group" href="[[+url]]">
   		<div class="f1-f2">
   			[[+board_name]]
   		</div>
        <div class="f3-f6 m-title">
            <div class="wrap">
                [[+sticky:if=`[[+sticky]]`:eq=`1`:then=`<span class="sticky tag">sticky</span>`]]
                [[+answered:notempty=`<span class="answered tag">solved</span>`]]
                [[+question:notempty=`<span class="question tag">question</span>`]]
                <!--<a class="h-group" href="[[+url]]">--><strong>[[+title]]</strong><!--</a>-->
            </div>
        </div>
        <div class="f7">
            [[+author_username]]
        </div>
        <div class="f8">
            [[+createdon:ago]]
        </div>
        <div class="f9">
            [[+thread_replies]]
        </div>
    </a>
</div>
