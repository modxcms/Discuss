<p class="dis-breadcrumbs">
    <a href="[[~[[*id]]]]">[[++discuss.forum_title]]</a> / [[%discuss.profile]]
</p>

<div class="dis-menu left" style="padding-right: 10px; width: 18%;">
    <h4>[[%discuss.forum_profile]]</h4>
    <ul>
        <li><a href="[[~[[*id]]]]user?user=[[+id]]">[[%discuss.view]]</a></li>
        [[+canEdit:notempty=`<li><a href="[[~[[*id]]]]user/edit">[[%discuss.edit]]</a></li>`]]
        <li><a href="[[~[[*id]]]]user/statistics?user=[[+id]]">[[%discuss.stats]]</a></li>
    </ul>
    
    [[+canAccount:notempty=`
    <h4>[[%discuss.account_settings]]</h4>
    <ul>
        <li><a href="[[~[[*id]]]]user/subscriptions">[[%discuss.subscriptions]]</a></li>
        <!--<li><a href="[[~[[*id]]]]user/preferences">[[%discuss.layout_preferences]]</a></li>-->
        <li><a href="[[~[[*id]]]]user/ignoreboards">[[%discuss.ignore_preferences]]</a></li>
    </ul>
    `]]

    <h4>[[%discuss.actions]]</h4>
    <ul>
        [[+modx.user.id:notempty=`<li><a href="[[~[[*id]]]]messages/new?user=[[+username]]">[[%discuss.send_pm]]</a></li>`]]
        [[+modx.user.id:notempty=`<li><a href="[[~[[*id]]]]user/merge">[[%discuss.account_merge]]</a></li>`]]
        [[+discuss.user.isAdmin:notempty=`<li><a href="[[~[[*id]]]]user/ban?u=[[+id]]">[[%discuss.ban_user]]</a></li>`]]
    </ul>
</div>