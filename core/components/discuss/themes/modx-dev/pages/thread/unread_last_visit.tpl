    <div class="dis-user-recent_posts f1-f9">
        <div class="dis-threads">
            [[+pagination:notempty=`<div class="paginate stand-alone horiz-list"> [[+pagination]]</div>`]]
            <ul class="dis-list">
                <li><h1>[[%discuss.unread_posts_last_visit]]</h1></li>
                [[+threads]]
            </ul>
            [[+pagination]]
        </div>
    </div>
    <aside>
        <hr class="line"/>
        <div class="PanelBox">
            [[!+discuss.user.id:notempty=`
            <div class="Box">
                <h4>[[%discuss.actions]]</h4>
                <p>[[+actionbuttons]]</p>
                <p>[[+moderators]]</p>
            </div>
            `]]
            [[!+discuss.user.id:is=``:then=`
            <div class="Box">
                <h4>[[%discuss.actions]]</h4>
                <p><a href="[[~[[*id]]]]login" class="Button">Login to Post</a></p>
            </div>
            `]]
            <div class="Box">
                <h4>[[%discuss.information]]</h4>
                <p>[[+readers]]</p>
            </div>
    </aside>
</div><!-- Close Content From Wrapper -->
[[+bottom]]




[