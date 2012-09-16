<!-- REMOVEPRODUCTION post/disboardpost.chunk.tpl -->
<div class="row h-group dis-category-[[+category]] [[+class]] [[+locked:is=`1`:then=`locked`:else=`unlocked`]] [[+unreadCls]]">
    <a class="h-group" href="[[+url]]">
        <div class="f1-f7">
            <div class="wrap">
                <strong>[[+sticky:if=`[[+sticky]]`:eq=`1`:then=`[[+title]]`:else=`[[+title]]`]]</strong>
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
