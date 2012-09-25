[[+top]]

<div class="dis-user-recent_posts">
    <div class="dis-threads">
        [[+pagination]]
        <ul class="dis-list">
            <li><h1>[[%discuss.user_posts? &user=`[[+name]]` &count=`[[+posts.total]]`]]</h1></li>
            [[+posts]]
        </ul>
    </div>

</div>

</div><!-- Close Content From Wrapper -->
[[+bottom]]

<aside>
    <hr class="line" />
    <div class="PanelBox">

        <div class="Box">
           <h4>[[+username]]'s Profile</h4>
            <ul class="panel_info">

                <li class="Heading"><img src="[[+avatarUrl]]" alt="[[+username]]" />
            <br /><span class="small">[[+title]]</span></li>
            </ul>

        </div>

        <div class="Box">
            [[+usermenu]]
        </div>
</aside>
