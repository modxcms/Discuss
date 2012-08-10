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
                            <li><a href="#">Back to MODX.com</a></li>
                            <li><a href="#">Forums</a></li>
                            <li><a href="#">Docs</a></li>
                            <li><a href="#">Bugs</a></li>
                        </ul>
                    </nav>
                    <a class="h-ir" href="#">MODX Forums</a>
                </div><!-- left side of masthead -->
                <div class="masthead-right f7-f12 m-all">
                    [[!+discuss.user.id:notempty=`
                    <div class="m-welcome_box">
                        Welcome back 
                    </div>
                    `]]
                    [[!+discuss.user.id:is=``:then=`
                    <div class="masthead-login m-login_box h-group">
                        <div class="masthead-title"><strong>Login to MODX</strong> Don't have a MODX.com account? <a href="#">Create one</a></div>
                            <form class="m-login_block">
                                <div class="f7-f8">
                                    <input type="text">
                                    <label>modx.com username</label>
                                </div>
                                <div class="f9-f10">
                                    <input type="password">
                                    <label>password</label>
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
                    <strong>[[%discuss.stats]]</strong>
                </div>
                <div class="f3-f4 m-stats">
                    <strong>Total:</strong>
                    [[%discuss.stats_totals?
                        &posts=`[[+totalPosts]]`
                        &threads=`[[+totalTopics]]`
                        &members=`[[+totalMembers]]`
                    ]]
                </div>
                <div class="f5-f6 m-stats">
                    <strong>Online:</strong>
                    [[%discuss.stats_online? 
                        &visitors=`[[+totalVisitorsActive]]` 
                        &members=`[[+totalMembersActive]]`
                    ]]
                </div>
                <div class="f7-f8 m-stats">
                [[%discuss.stats_today?
                    &hits=`<span class="m-stats-single">[[+activity.hits]]</span>`
                    &topics=`<span class="m-stats-single">[[+activity.topics]]</span>`
                    &replies=`<span class="m-stats-single">[[+activity.replies]]</span>`
                    &visitors=`<span class="m-stats-single">[[+activity.visitors]]</span>`
                ]]
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
      .script("http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js").wait()
      .script("http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js")
      .script("[[++modx.assets.url]]js/modernizr-1.6.min.js")
      .script("[[++modx.assets.url]]js/jquery.cycle.all.min.js").wait()
      .script("[[++modx.assets.url]]js/script.js") 
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

