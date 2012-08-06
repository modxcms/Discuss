<!doctype html>
<!-- wrapper.tpl -->
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <!--[if IE]><![endif]-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <base href="[[!++site_url]]">
    <title>MODX :: [[!+discuss.pagetitle]]</title>
    <meta name="title" content="[[!+discuss.pagetitle]]">
    <meta name="author" content="MODX Systems, LLC">
    <link href="http://get.gridsetapp.com/2953/" rel="stylesheet" />
    [[-<link rel="stylesheet" href="http://modx.com/assets/css/forums.css?v=101">]]
    <link href="//get.pictos.cc/fonts/2455/2" rel="stylesheet" type="text/css">
    <script src="http://get.gridsetapp.com/2953/overlay/"></script>
    [[*cssjs]]
    
    [[- Live Typekit call
    [[++discuss.load_typekit:notempty=`<!-- TypeKit -->
    <script src="http://use.typekit.com/zub5doo.js"></script>
    <script type="text/javascript">try{Typekit.load();}catch(e){}</script>`]]
    <script src="[[++modx.assets.url]]js/LABjs/LAB.min.js"></script> 
    ]]

    [[- local typekit call]]
    <script type="text/javascript" src="//use.typekit.net/ukf1ncb.js"></script>
    <script type="text/javascript">try{Typekit.load();}catch(e){}</script>
</head>
<body id="body-[[+controller.controller]]" class="new-forums">[[-we can remove this class and the sass line if integrated into a new design at a later date]]

    [[-
<!--     REMOVE THIS STUFF EVENTUALLY
    <div id="overlay-20"> </div>
    [[+discuss.user.isModerator:is=`1`:then=`<div class="dis-sticky-actions"><div class="full-width">[[+threadactionbuttons]]</div></div>`]]

    <div id="header">
        <a href="#main" class="hidden">Skip to content</a>
        <header class="container">
<nav id="global">
            <a href="/?category=2" class="global1[[+category]]">General</a>
            <a href="/?category=3" class="global2[[+category]]">Revolution</a>
            <a href="/?category=4" class="global3[[+category]]">Evolution</a>
            <a href="/?category=5" class="global4[[+category]]">Add-ons</a>
            <a href="/?category=6" class="global5[[+category]]">International</a>
</nav>
        <nav id="global2">
            [[+discuss.user.id:is=``:then=`<a href="[[~[[*id]]]]login">Login</a> | <a href="[[~[[*id]]]]register">Register</a>`]]
            [[+discuss.user.id:notempty=`Welcome, <a href="[[~[[*id]]]]user/?user=[[+discuss.user.id]]">[[+modx.user.username]]</a> | <a href="[[~[[*id]]]]logout">Logout</a>`]]
             | <a href="http://www.modx.com" title="Shimmy on over to MODX.com">MODX.com</a>
        </nav>


          <nav id="logo_search">
            <a href="[[~[[*id]]]]" class="ir" id="logo" title="Open Source PHP Content Management System, Framework, Platform and More">MODX Open Source Content Management System, Framework, Platform and More.</a>
            <div id="search">
            <div class="links">
                <a href="[[~54]]">Find a Partner</a>  <span class="ir">|</span>
                <a href="[[~56]]">Hosts + SaaS</a> <span class="ir">|</span> 
                <a href="[[~30]]">Jobs</a> <span class="ir">|</span> 
                <a href="[[~109]]">Donate</a></div>

                        <form action="[[~[[*id]]]]search" method="get" accept-charset="utf-8">
                            <label for="search_form_input" class="hidden">Search</label>
                            <input id="search_form_input" placeholder="Search keyphrase..." name="s" value="" title="Start typing and hit ENTER" type="text">
                            <input value="Go" type="submit">
                        </form>
            </div>
          </nav>

        </header>
    </div>
    REMOVE THIS STUFF EVENTUALLY -->
    ]]



    <!-- NEW masthead 2012 start -->
    <header class="masthead">
        <div class="wrapper h-group">
            <div class="f-padinfull f-all">
                <div class="f1-f6">
                    <nav class="l-col_16">
                        <ul class="m-sm_nav_pod">
                            <li><a href="#">Back to MODX.com</a></li>
                            <li><a href="#">Forums</a></li>
                            <li><a href="#">Docs</a></li>
                            <li><a href="#">Bugs</a></li>
                        </ul>
                    </nav>
                    <a class="h-ir" href="#">MODX Forums</a>
                </div><!-- left side of masthead -->
                <div class="f7-f12">
                    <div class="masthead-login m-login_box h-group">
                        <div class="masthead-title"><strong>Login to MODX</strong> Don't have a MODX.com account? <a href="#">Create one</a></div>
                            <form class="m-login_block">
                                <div class="f7-f8">
                                    <input type="text">
                                    <label>modx.com username</label>
                                </div>
                                <div class="f9-f10">
                                    <input type="text">
                                    <label>password</label>
                                </div>
                                <div class="f11-f12">
                                    <input type="submit">
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="breadcrumbs f-padinfull l-horizontal_nav h-group">
       New Bread Crumbs go here
    </div>

    <!-- / NEW mastehad 2012 start -->



    <!-- #header -->

<div>
    <div id="section_wrap">


        [[- <!-- REMOVE THIS STUFF EVENTUALLY
        <header class="container">
            <nav id="section">
                <ul>
                    [[+discuss.user.id:is=``:then=`<li class="first level1">
                        <a href="[[~[[*id]]]]register" class="first level1"><span class="Title">Register</span>Sign Up with the MODX Community</a></li>
                    <li class="level1"><a href="[[~[[*id]]]]login" class="first level1"><span class="Title">Login</span>Use Your MODX.com Account</a></li>`]]
                    [[+discuss.user.id:notempty=`<li class="first level1 parent">
                        <a href="[[~[[*id]]]]thread/unread" class="first level1 parent"><span class="Title">View Unread Posts</span> All Discussion Categories</a>
                        <ul class="inner">
                            <li class="first level2 parent"><a href="[[~[[*id]]]]thread/unread_last_visit" class=""><span class="Title">View New</span>Posts Since Last Visit</a></li>
                            <li class="first level2 parent"><a href="[[~[[*id]]]]thread/new_replies_to_posts" class=""><span class="Title">New Replies</span>[[%discuss.new_replies_to_posts]]</a></li>
                            <li class="first level2 parent"><a href="[[~[[*id]]]]thread/recent" class=""><span class="Title">Recent Posts</span>Latest Posts</a></li>
                        </ul>
                    </li>
                    <li class="level1">
                    <a href="[[~[[*id]]]]messages/" class="level1"><span class="Title">Private Discussions</span> All Private Messages</a>
                    </li>`]]
                </ul>
            </nav>
        </header>
        REMOVE THIS STUFF EVENTUALLY -->
        ]]
[[+trail]]

    </div>
        [[-<!-- <div id="frame">
            <div id="body"> -->]]
                    <div class="wrapper l-center f-padinfull">
                        [[+content]]
                    </div>
                <!-- Close Content Inside home.tpl -->
            [[-<!-- </div>
        </div> -->]]

</div>
[[$tplOmega-2012]]