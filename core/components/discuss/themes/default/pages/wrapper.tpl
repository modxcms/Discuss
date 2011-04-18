<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <base href="[[++site_url]]" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
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
                    <li><a href="[[~[[*id]]]]user/?user=[[+discuss.user.id]]">[[+modx.user.username]]</a></li>
                    <li><a href="http://modx.com/">modx.com</a></li>
                    <li><a href="[[~[[*id]]]]search">Search</a></li>
                    <li><a href="[[~[[*id]]]]user/?user=[[+discuss.user.id]]">Profile</a></li>
                    <li class="last">[[+discuss.authLink]]</li>
                </ul>
            </div>
            <div id="metanav">
            </div>
        </div>
        <div class="clear">&nbsp;</div>
        <div id="mainheader">
            <div id="avvy" style="float: right; padding: 5px;">
                <div style="float: right;"><img src="[[+discuss.user.avatar_url]]" alt="" /></div>
                <div style="float: left; padding-right: 5px;"><a href="[[~[[*id]]]]thread/unread">View Unread Posts</a><br /></div>
            </div>
            <h1 id="logo" class="pngfix"><a href="[[~4]]"><span>modx</span></a></h1>
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
</body>
</html>