<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <base href="[[++site_url]]" />
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <title>[[*pagetitle]] | MODx CMS / CMF</title>
    [[*cssjs]]
</head>
<body>
<!-- start header -->
<div id="header">
    <div class="">
        <div id="metaheader">
            <div id="signin">
                <ul>
                    [[+discuss.user.id:notempty=`<li><a href="[[~[[*id]]]]user/?user=[[+discuss.user.id]]">[[+modx.user.username]]</a></li>`]]
                    <li><a href="http://modx.com/">modx.com</a></li>
                    <li><a href="[[~[[*id]]]]search">Search</a></li>
                    [[+discuss.user.id:notempty=`<li><a href="[[~[[*id]]]]user/?user=[[+discuss.user.id]]">Profile</a></li>`]]
                    [[+discuss.user.id:notempty=`<li><a href="[[~[[*id]]]]messages/">Messages ([[+discuss.user.unread_messages]])</a></li>`]]
                    [[+discuss.user.id:is=``:then=`<li><a href="[[~[[*id]]]]register">Register</a></li>`]]
                    <li class="last">[[+discuss.authLink]]</li>

                </ul>
            </div>
            <div id="metanav">
            </div>
        </div>
        <div class="clear">&nbsp;</div>
        <div id="mainheader">
            <div id="avvy" style="float: right; padding: 5px;">
                [[+discuss.user.avatar_url:notempty=`<div style="float: right;"><img src="[[+discuss.user.avatar_url]]" alt="" /></div>`]]
                <div style="float: left; padding-right: 5px; text-align: right;">
                    [[+discuss.user.id:notempty=`<a href="[[~[[*id]]]]thread/unread">View Unread Posts</a><br />`]]
                    [[+discuss.user.id:notempty=`<a href="[[~[[*id]]]]thread/unread_last_visit">View Unread Posts Since Last Visit</a><br />`]]
                    [[+discuss.user.id:notempty=`<a href="[[~[[*id]]]]thread/new_replies_to_posts">[[%discuss.new_replies_to_posts]]</a><br />`]]
                    <a href="[[~[[*id]]]]thread/recent">View Recent Posts</a><br />
                </div>
            </div>
            <h1 id="logo" class="pngfix"><a href="[[~[[*id]]]]"><span>modx</span></a></h1>
        </div>
        <div class="clear">&nbsp;</div>
    </div>
</div>
<!-- end header -->

<div>
    <div class="discuss" style="width: 95%; margin: 0 auto;">
        [[+content]]
    </div>
    <div class="clear">&nbsp;</div>
</div>
<!-- Discuss version [[+discuss_version]] -->
</body>
</html>