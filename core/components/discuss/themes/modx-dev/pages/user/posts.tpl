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

    <aside class="f10-f12">
        <div class="PanelBox">
            <div class="Box">
               <h4>[[+username]]'s Profile</h4>
                <ul class="panel_info">
                    <li class="Heading">
                        <a href="https://en.gravatar.com/site/login#your-images">
                            <img src="[[+avatarUrl]]" alt="[[+username]]" />
                        </a>
                        <span class="small">[[+title]]</span>
                    </li>
                </ul>
            </div>
            <div class="Box">
                [[+usermenu]]
            </div>
        </div>
    </aside>
</div><!-- Close Content From Wrapper -->
[[+bottom]]