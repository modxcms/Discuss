<div class="row l-table h-group dis-category-[[+category]] [[+class]] [[+locked:is=`1`:then=`locked`:else=`unlocked`]] [[+unreadCls]]">
   <a class="h-group" href="[[+url]]">
        <div class="f1-f7 l-vmiddle m-title">
            <div class="wrap">
                [[+sticky:eq=`1`:then=`<span class="sticky tag">[[%discuss.sticky]]</span>`]]
                [[+locked:is=`1`:then=`<span class="locked tag">[[%discuss.board_locked]]</span>`:else=``]]
                [[+answered:eq=`1`:then=`<span class="answered tag">[[%discuss.solved]]</span>`:default=`
                    [[+class_key:eq=`disThreadQuestion`:then=`<span class="question tag">[[%discuss.question]]</span>`:else=``]]
                `]]
                <strong>[[+title]]</strong>
                [[+thread_pagination]]
            </div>
        </div>
        <div class="f8 l-vmiddle l-txtcenter">[[+views]]</div>
        <div class="f9 l-vmiddle l-txtcenter">[[+replies]]</div>
        <div class="f10-f12">
            <p class="posted-by">[[%discuss.board_by]]: [[+last_post_username]], [[+createdon:ago]]</p>
            <p class="posted-by">[[%discuss.board_last]]: [[+first_post_username]], [[+first_post_createdon:ago]]</p>
        </div>
    </a>
</div>
