<p>Using common page tpl</p>
<div class="f1-f9 twelve-form">
    [[+form]]
    [[+thread_posts:notempty=`
        <h1>[[%discuss.thread_summary]]</h1>
        <ul class="dis-list h-group">
            [[+thread_posts]]
        </ul>
    `]]
</div>
[[+bottom]]
[[+sidebar]]
