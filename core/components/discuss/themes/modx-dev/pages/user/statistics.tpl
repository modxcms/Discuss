    <div class="dis-profile">
        <h1>[[%discuss.general_stats? &user=`[[+name]]`]]</h1>
        <ul class="profile">
            <li>[[%discuss.joined]]: <strong>[[+confirmedon:strtotime:date=`%b %d, %Y %I:%M %p`]]</strong></li>
            <li>[[%discuss.last_login]]: <strong>[[+last_login:strtotime:date=`%b %d, %Y %I:%M %p`]]</strong></li>
            <li>[[%discuss.last_active]]: <strong>[[+last_active:strtotime:date=`%b %d, %Y %I:%M %p`]]</strong></li>
            <li>[[%discuss.post_count]]: <strong>[[+posts]]</strong></li>
            <li>[[%discuss.threads_started]]: <strong>[[+topics]]</strong></li>
            <li>[[%discuss.replies]]: <strong>[[+replies]]</strong></li>
            <li>[[%discuss.location]]: <strong>[[+location]]</strong></li>
        </ul>
    </div>
</div><!-- Close Content From Wrapper -->
[[+bottom]]

<aside>
    <hr class="line"/>
    <div class="PanelBox">
        <div class="Box">
            <h4>[[+username]]'s Profile</h4>
            <ul class="panel_info">
                <li class="Heading">
                    <a href="https://en.gravatar.com/site/login#your-images"><img src="[[+avatarUrl]]" alt="[[+username]]"/></a>
                    <br/><span class="small">[[+title]]</span>
                </li>
            </ul>
        </div>
        <div class="Box">
            [[+usermenu]]
        </div>
</aside>
