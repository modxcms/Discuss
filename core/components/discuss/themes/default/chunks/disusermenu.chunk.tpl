<h4>User Menu</h4>
<ul class="panel_info">
    <li class="Heading">[[%discuss.forum_profile]]</li>

    <li class="Depth2"><strong><a href="[[DiscussUrlMaker? &action=`user` &params=`{"type":"username", "user":"[[+username]]"}`]]">[[%discuss.view]]</a></strong><span class="Count">&nbsp;</span></li>
    [[+canEdit:notempty=`<li class="Depth2"><strong><a href="[[DiscussUrlMaker? &action=`user/edit`]]">[[%discuss.edit]]</a></strong><span class="Count">&nbsp;</span></li>`]]
    <li class="Depth2"><strong><a href="[[DiscussUrlMaker? &action=`user/statistics` &params=`{"user":"[[+id]]"}`]]">[[%discuss.stats]]</a></strong><span class="Count">&nbsp;</span></li>
    <li class="Depth2"><strong><a href="[[DiscussUrlMaker? &action=`user/posts` &params=`{"user":"[[+id]]"}`]]">[[%discuss.posts]]</a></strong><span class="Count">&nbsp;</span></li>


    <li class="Heading">[[%discuss.account_settings]]</li>
    [[+canAccount:notempty=`<li class="Depth2"><strong><a href="[[DiscussUrlMaker? &action=`user/subscriptions`]]">[[%discuss.subscriptions]]</a></strong><span class="Count">&nbsp;</span></li>`]]

    [[+canAccount:notempty=`<li class="Depth2"><strong><a href="[[DiscussUrlMaker? &action=`user/ignoreboards`]]">[[%discuss.ignore_preferences]]</a></strong><span class="Count">&nbsp;</span></li>`]]
    [[+modx.user.id:notempty=`<li class="Depth2"><strong><a href="[[DiscussUrlMaker? &action=`messages/new` &params=`{"user":"[[+username]]"}`>[[%discuss.send_pm]]</a></strong><span class="Count">&nbsp;</span></li>`]]
    [[+canMerge:notempty=`<li class="Depth2"><strong><a href="[[DiscussUrlMaker? &action=`user/merge`]]">[[%discuss.account_merge]]</a></strong><span class="Count">&nbsp;</span></li>`]]
    [[+discuss.user.isAdmin:notempty=`<li class="Depth2"><strong><a href="[[DiscussUrlMaker? &action=`user/ban` &params=`{"u":"[[+id]]"}`]]">[[%discuss.ban_user]]</a></strong><span class="Count">&nbsp;</span></li>`]]
</ul>