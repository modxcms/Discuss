<p class="dis-breadcrumbs">
    <a href="[[~[[*id]]]]">[[++discuss.forum_title]]</a> / [[%discuss.profile]]
</p>

<div class="dis-menu left" style="padding-right: 10px; width: 18%;">
    <h4>[[%discuss.forum_profile]]</h4>
    <ul>
        <li><a href="[[~[[*id]]]]user?user=[[+id]]">[[%discuss.view]]</a></li>
        [[+canEdit:notempty=`<li><a href="[[~[[*id]]]]user/edit?user=[[+id]]">[[%discuss.edit]]</a></li>`]]
        <li><a href="[[~[[*id]]]]user/statistics?user=[[+id]]">[[%discuss.stats]]</a></li>
        <!--<li><a href="[[~[[*id]]]]user/track?user=[[+id]]">[[%discuss.track]]</a></li>-->
    </ul>
    
    [[+canAccount:notempty=`
    <h4>[[%discuss.account_settings]]</h4>
    <ul>
        <li><a href="[[~[[*id]]]]user/account?user=[[+id]]">[[%discuss.account]]</a></li>
        <li><a href="[[~[[*id]]]]user/notifications?user=[[+id]]">[[%discuss.notifications]]</a></li>
        <!--<li><a href="[[~[[*id]]]]user/preferences?user=[[+id]]">[[%discuss.layout_preferences]]</a></li>-->
        <li><a href="[[~[[*id]]]]user/ignoreboards?user=[[+id]]">[[%discuss.ignore_preferences]]</a></li>
    </ul>
    `]]
</div>