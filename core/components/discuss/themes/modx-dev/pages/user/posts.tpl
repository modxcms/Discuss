[[+top]]

    <div class="dis-user-recent_posts f1-f9">
        <div class="dis-threads">
            [[+pagination:notempty=`<div class="paginate stand-alone horiz-list"> [[+pagination]]</div>`]]
            <ul class="dis-list">
                <li><h1>[[%discuss.user_posts? &user=`[[+name]]` &count=`[[+posts.total]]`]]</h1></li>
                [[+posts]]
            </ul>
        </div>
    </div>

    [[+sidebar]]
</div><!-- Close Content From Wrapper -->
[[+bottom]]
