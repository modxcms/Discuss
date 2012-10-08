<p class="dis-breadcrumbs">
    <a href="[[DiscussUrlMaker]]">[[++discuss.forum_title]]</a> / [[%discuss.profile]]
</p>

<div class="dis-menu left" style="padding-right: 10px; width: 18%;">
    <h4>[[%discuss.forum_profile]]</h4>
    <ul>
        <li><a href="[[DiscussUrlMaker? &action=`user` &params=`{"type":"userid","user":"[[+id]]"}`]]">[[%discuss.view]]</a></li>
        [[+canEdit:notempty=`<li><a href="[[DiscussUrlMaker? &action=`user/edit`]]">[[%discuss.edit]]</a></li>`]]
        <li><a href="[[DiscussUrlMaker? &action=`user/statistics` &params=`{"user":"[[+id]]"}`]]">[[%discuss.stats]]</a></li>
    </ul>
    
    [[+canAccount:notempty=`
    <h4>[[%discuss.account_settings]]</h4>
    <ul>
        <li><a href="[[DiscussUrlMaker? &action=`user/subscriptions`]]">[[%discuss.subscriptions]]</a></li>
        <!--<li><a href="[[DiscussUrlMaker? &action=`user/preferences`]]">[[%discuss.layout_preferences]]</a></li>-->
        <li><a href="[[DiscussUrlMaker? &action=`user/ignoreboards`]]">[[%discuss.ignore_preferences]]</a></li>
    </ul>
    `]]

    <h4>[[%discuss.actions]]</h4>
    <ul>
        [[+modx.user.id:notempty=`<li><a href="[[DiscussUrlMaker? &action=`messages/new` &params=`{"user":"[[+username]]"}`]]">[[%discuss.send_pm]]</a></li>`]]
        [[+canMerge:notempty=`<li><a href="[[DiscussUrlMaker? &action=`user/merge`]]">[[%discuss.account_merge]]</a></li>`]]
        [[+discuss.user.isAdmin:notempty=`<li><a href="[[DiscussUrlMaker? &action=`user/ban` &params=`{"u":"[[+id]]"}`]]">[[%discuss.ban_user]]</a></li>`]]
    </ul>
</div>