<div class="row h-group dis-category-[[+category]] [[+class]] [[+locked:is=`1`:then=`locked`:else=`unlocked`]] [[+unreadCls]]">
   <a class="h-group" href="[[+url]]">
        <div class="f1-f7 m-title">
            <div class="wrap">
                [[+sticky:eq=`1`:then=`<span class="sticky tag">[[%discuss.sticky]]</span>`]]
                [[+answered:eq=`1`:then=`<span class="answered tag">[[%discuss.solved]]</span>`:default=`
                    [[+class_key:eq=`disThreadQuestion`:then=`<span class="question tag">[[%discuss.question]]</span>`:else=``]]
                `]]

                <strong>[[+title]]</strong>
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
