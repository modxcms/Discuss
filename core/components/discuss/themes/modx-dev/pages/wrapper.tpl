<!doctype html>

<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <!--[if IE]><![endif]-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <base href="[[!++site_url]]">
    <title>[[!+discuss.pagetitle]] | MODX Community Forums </title>
    <meta name="title" content="[[!+discuss.pagetitle]]">
    <meta name="author" content="MODX Systems, LLC">

    [[- <!-- @todo merge these into one --> ]]
    <link href="[[+discuss.config.cssUrl]]redo/index.css" rel="stylesheet" type="text/css">
    <link href="[[+discuss.config.cssUrl]]redo/forums-styles.css" rel="stylesheet" type="text/css">
    <link href="[[+discuss.config.cssUrl]]redo/build.css" rel="stylesheet" type="text/css">
    <link href="[[+discuss.config.cssUrl]]jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
    <link href="http://get.gridsetapp.com/2953/" rel="stylesheet" />

    [[*cssjs]]

    [[- Live Typekit call
    [[++discuss.load_typekit:notempty=`<!-- TypeKit -->
    <script src="http://use.typekit.com/zub5doo.js"></script>
    <script type="text/javascript">try{Typekit.load();}catch(e){}</script>`]]
    ]]
    <script src="[[+discuss.config.jsUrl]]LABjs/LAB.min.js"></script>

    [[- local typekit call]]
    <script type="text/javascript" src="//use.typekit.net/ukf1ncb.js"></script>
    <script type="text/javascript">try{Typekit.load();}catch(e){}</script>
</head>
<body id="body-[[+controller.controller]]">

[[+discuss.user.isModerator:is=`1`:then=`
    <div class="dis-sticky-actions"><div class="full-width">[[+threadactionbuttons]]</div></div><!-- / moderator bar-->
`]]

    <!-- NEW masthead 2012 start -->
    <header class="masthead">
        <div class="wrapper h-group">
            <div class="f-padinfull f-all m-all">
                <div class="f1-f6 m-all">
                    <nav class="l-col_16">
                        <ul class="m-sm_nav_pod">
                            <li><a href="http://modx.com/">Back to MODX.com</a></li>
                            <li><a href="[[~[[++discuss.forums_resource_id]]]]">Forums</a></li>
                            <li><a href="http://rtfm.modx.com/">Docs</a></li>
                            <li><a href="http://tracker.modx.com/">Bugs</a></li>
                        </ul>
                    </nav>
                    <a class="h-ir" href="#">MODX Forums</a>
                </div><!-- left side of masthead -->
                <div class="masthead-right f7-f12 m-all">
                    [[!+discuss.user.id:notempty=`
                    <div class="m-welcome_box">
                        <div class="m-user_box h-group">
                            <div class="l-left">
                                <a href="[[~[[++discuss.forums_resource_id]]]]u/[[!+discuss.user.username]]"><img src="[[!+discuss.user.avatar_url]]"></a>
                                <br />
                                <span class="m-user_posts">[[!+discuss.user.posts_formatted]] [[%discuss.posts]]</span>
                            </div>
                            <div class="l-right">
                                <h3>Welcome Back <a href="[[~[[++discuss.forums_resource_id]]]]u/[[!+discuss.user.username]]">[[!+discuss.user.name_first]]</a></h3>
                                <p>You have <a href="[[~[[++discuss.forums_resource_id]]]]thread/unread_last_visit">[[!+discuss.user.unread_posts]]</a>,
                                    <a href="[[~[[++discuss.forums_resource_id]]]]messages/" title="View Messages">[[!+discuss.user.unread_messages]]</a>
                                    and <a href="[[~[[++discuss.forums_resource_id]]]]thread/new_replies_to_posts">[[!+discuss.user.new_replies]]</a> to read.<br />
                                    [[!+discuss.user.no_replies_count:gte=`1`:then=`
                                        [[!+discuss.user.unanswered_questions_count:gte=`1`:then=`
                                            Please help the community with <a href="[[~[[++discuss.forums_resource_id]]]]thread/unanswered_questions">[[!+discuss.user.unanswered_questions]]</a>
                                            or <a href="[[~[[++discuss.forums_resource_id]]]]thread/no_replies">[[!+discuss.user.no_replies]]</a>.
                                        `:else=`
                                            You can contribute to <a href="[[~[[++discuss.forums_resource_id]]]]thread/no_replies">[[!+discuss.user.no_replies]]</a>.
                                        `]]
                                    `:else=`
                                        [[!+discuss.user.unanswered_questions_count:gte=`1`:then=`
                                            You can help answer <a href="[[~[[++discuss.forums_resource_id]]]]thread/unanswered_questions">[[!+discuss.user.unanswered_questions]]</a>.
                                        `:else=`
                                            Wow! No unanswered questions or discussions without replies.
                                        `]]
                                    `]]
                                </p>
                                <div class="m-user_tools">
                                    <ul class="m-user_tools_reg_links l-horiz_list">
                                        <li><a href="[[~[[++discuss.forums_resource_id]]]]u/[[!+discuss.user.username]]">my profile</a></li>
                                        <!--<li><a href="[[~[[++discuss.forums_resource_id]]]]user/subscriptions">update email notifications</a></li>-->
                                    </ul>
                                    <a class="m-user_tools_logout" href="[[~[[++discuss.login_resource_id]]? &service=`logout` &discuss=`1`]]">Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    `]]
                    [[!+discuss.user.id:is=``:then=`
                    <div class="masthead-login m-login_box h-group">
                        <div class="masthead-title"><strong>Login to MODX</strong> Don't have a MODX.com account? <a href="#">Create one</a></div>
                            <form class="m-login_block" method="post" action="[[~[[++discuss.login_resource_id]]]]">
                                <input type="hidden" name="service" value="login" />
                                <input type="hidden" name="discussPlace" value="[[!+discuss.place]]" />
                                <div class="f7-f8">
                                    <input type="text" name="username" id="login-username">
                                    <label for="login-username">modx.com username</label>
                                </div>
                                <div class="f9-f10">
                                    <input type="password" name="password" id="login-password">
                                    <label for="login-password">password</label>
                                </div>
                                <div class="f11-f12">
                                    <input class="alt-1-cta" type="submit" value="Login">
                                </div>
                            </form>
                    </div>
                    `]]
                </div>
            </div>
        </div>
    </header>
    [[+trail]]
    <!-- / NEW mastehad 2012 -->
    <div class="wrapper l-center f-padinfull h-group">
        [[+content]]
    </div>

<!-- move all this to tplOmega-2012 eventually -->
    <footer class="h-group">
        <!-- forum stats -->
        <div class="f-padinfull">
            <div class="footer-stats h-group">
                <div class="f1-f2">
                    <strong class="m-stats-title">[[%discuss.stats]]</strong>
                </div>
                <div class="f3-f4 m-stats">
                    <strong class="m-stats-title">Total:</strong>
                    <span class="m-stats-single">[[+totalPosts]]</span> [[%discuss.posts]]<br />
                    <span class="m-stats-single">[[+totalTopics]]</span> [[%discuss.threads]]<br />
                    <span class="m-stats-single">[[+totalMembers]]</span> [[%discuss.members]]
                </div>
                <div class="f5-f6 m-stats">
                    <strong class="m-stats-title">Online:</strong>
                    <span class="m-stats-single">Visitors: [[+totalVisitorsActive]]</span><br />
                    <span class="m-stats-single">Members: [[+totalMembersActive]]</span><br />
                </div>
                <div class="f7-f8 m-stats">
                    <strong class="m-stats-title">Today:</strong>
                    <span class="m-stats-single">Visitors: [[+activity.visitors]]</span><br />
                    <span class="m-stats-single">[[%discuss.threads]]: [[+activity.topics]]</span><br />
                    <span class="m-stats-single">[[%discuss.replies]]: [[+activity.replies]]</span>
                </div>
                <div class="f9-f12">
                    [[+activeUsers]]
                </div>
            </div>
        </div>
        <!-- forum stats -->

        <div class="f-padinfull">
            <div class="f1-f8">
                <nav class="group">
                    <ul class="horiz-list">
                        <li><a href="doc/">MODX Cloud User Guide</a></li>
                        <li><a href="doc/api/">API Documentation</a></li>
                        <li><a href="contact.html">Contact Us</a></li>
                        <li><a href="signup/">Sign-up now</a></li>
                    </ul>
                </nav>
                <div class="group">
                    <div class="f1-f2">
                        <a class="m-modx_logo h-ir" href="http://modx.com">MODX Creative Freedom</a>
                    </div>
                    <div class="group copy-info f3-f7">
                        <p>&copy; MODX, LLC 2012. All Rights Reserved.</p>
                        <ul class="horiz-list">
                            <li><a href="trademark.html">Trademark Policy</a></li>
                            <li><a href="terms.html">Terms of Service</a></li>
                            <li><a href="privacy.html">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="f9-f12 stay-connected">
                <h3>Stay Connected</h3>
                <form id="newsletter" action="http://modxcms.list-manage.com/subscribe/post" method="post">
                    <input type="hidden" name="u" value="08b25a8de68a29fe03a483720" />
                    <input type="hidden" name="id" value="848cf40420" />
                    <input type="hidden" name="source" value="www_1" id="source">
                    <input type="hidden" name="MERGE7" value="http://modx.com/" id="MERGE7">
                    <div class="field clearfix">
                        <label for="MERGE0" class="hidden">Your email</label>
                        <input type="text" placeholder="you@example.com" required id="MERGE0" name="MERGE0" value="" class="textbox" />
                        <input  type="submit" name="Submit" value="Sign up" />
                    </div>
                </form> 
                <ul class="social-icons horiz-list">
                    <li><a href="http://twitter.com/#!/modxcms" title="MODX on Twitter" class="twitter">Twitter</a></li>
                    <li><a href="http://www.facebook.com/modxcms" title="MODX Facebook" class="facebook">Facebook</a></li>
                    <li><a href="http://modx.com/feeds/" title="Subscribe to our Feeds" class="rss">RSS Feeds</a></li>
                </ul>
            </div>
        </div>
    </footer><!-- footer -->

    <!-- not sure what this is used for? -->
    <div class="overlay[[*id:ne=`320`:then=` round7`]]" id="overlay">
        <div class="contentWrap"></div>
    </div>
    <!-- / not sure what this is used for? -->

[[*template:ne=`12`:then=`
    <script>
      $LAB
      .setOptions({"AlwaysPreserveOrder":true})
      .script("http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js").wait()
      .script("[[+discuss.config.jsUrl]]jquery.scrollTo-min.js")
      .script("[[+discuss.config.jsUrl]]jquery-ui-1.8.16.custom.min.js").wait()
      .script("[[+discuss.config.jsUrl]]discuss.js")
      .script("[[+discuss.config.jsUrl]]sh/shCore.js").wait()
      .script("[[+discuss.config.jsUrl]]sh/shAutoloader.js")
      .script("[[+discuss.config.jsUrl]]sh/shDiscuss.js")
      .script("[[+discuss.config.jsUrl]]dis.sticky.js")
      .script("[[+discuss.config.jsUrl]]redo/modernizr.custom.07525.js")

      [[*lastJSinherit]] [[*lastJS]] ;
    </script>
    <script>
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-71684-1']);
      _gaq.push(['_setDomainName', '.modx.com']);
      _gaq.push(['_setAllowLinker', true]);
      _gaq.push(['_setAllowHash', false]);
      _gaq.push(['_trackPageview']);
    [[*id:ne=`211`:then=``:else=`  _gaq.push(['_trackPageview', '/404/?page=' + document.location.pathname + document.location.search + '&from=' + document.referrer]);`]]
      (function() { 
        var ga = document.createElement('script');
        ga.type = 'text/javascript';
        ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'; 
        var s = document.getElementsByTagName('script')[0]; 
        s.parentNode.insertBefore(ga, s);
      })(); 
    </script>
    <!--[if lt IE 7 ]><script src="[[++modx.assets.url]]js/dd_belatedpng.js?v=1"></script><![endif]-->
`]]
[[*beforeClose]]
<!-- remove for production--><script src="http://get.gridsetapp.com/2953/overlay/"></script>
</body>
</html>

<!-- / end move -->

