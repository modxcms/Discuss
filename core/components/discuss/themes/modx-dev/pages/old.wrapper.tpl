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
<body id="body-[[+controller.controller]]" class="new-forums">[[-we can remove this class and the sass line if integrated into a new design at a later date]]

[[+discuss.user.isModerator:is=`1`:then=`<!-- moderator bar--><div class="dis-sticky-actions"><div class="full-width">[[+threadactionbuttons]]</div></div><!-- / moderator bar-->`]]

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
   

    <!-- / NEW mastehad 2012 start -->



    <!-- #header -->

<div>
    <div id="section_wrap"><!--section wrap-->


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
        ]]<!--trail-->
[[+trail]]<!-- /trail -->

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
<!-- move all this to tplOmega-2012 eventually -->
<footer class="h-group">
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
                <a class="modx-logo ir" href="http://modx.com">MODX Creative Freedom</a>
                <div class="group copy-info">
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
</footer>



[[-<!-- <footer>
    <a href="[[AnchorMan? &anchor=`header`]]" id="top" class="ir clearfix">Back to Top</a>
    <section id="subscribe">
        <div class="container clearfix">
            <section id="address">
                <h3>MODX Global HQ</h3>
                <address style="color:#fff">
                    1333 N Stemmons Fwy, Ste 110<br />
                    Dallas, TX 75207<br />
                    United States<br /><br />
                    +1 (469) 777-MODX (6639)
                </address>
            </section>
            <section id="company">
[[Wayfinder?
  &startId=`12`
  &contexts=`web` 
  &level=`1`
  &displayStart=`1`
  &scheme=`full`
  &startItemTpl=`companyNavStart`
]]
           </section>

           <section id="sponsors">
               <h3>Sponsors</h3>
               <a href="http://www.softlayer.com/modx#utm_source=modx&utm_medium=banners&utm_content=footer&utm_campaign=sponsorad" class="ir" id="softlayer" title="SoftLayer: Dedicated Server Hosting, Cloud Servers, &amp; Managed Hosting Plans">SoftLayer</a>
               <a  href="http://firehost.com/" class="ir last" id="firehost" title="Firehost: Secure Cloud Hosting">Firehost: Secure Cloud Hosting</a>
           </section>

           <section id="connect" class="clearfix">
           <form id="newsletter" action="http://modxcms.list-manage.com/subscribe/post" method="post">
             <h3>Stay Connected</h3>
             <input type="hidden" name="u" value="08b25a8de68a29fe03a483720" />
             <input type="hidden" name="id" value="848cf40420" />
             <input type="hidden" name="source" value="www_[[*id]]" id="source">
             <input type="hidden" name="MERGE7" value="[[~[[*id]]? &scheme=`full`]]" id="MERGE7">
             <div class="field clearfix">
                  <label for="MERGE0" class="hidden">Your email</label>
                  <input type="text" placeholder="you@example.com" required id="MERGE0" name="MERGE0" value="" class="textbox" />
                  <input  type="submit" name="Submit" value="Sign up" />
             </div>
             <p><a href="[[~458]]">Read our previous email newsletters</a>.</p>
<div id="social_connect">
  <a href="http://twitter.com/#!/modxcms" title="MODX on Twitter">
    <img src="[[++modx.assets.url]]i/social/twitter-2.png" alt="Twitter" title="Follow MODX on Twitter">
  </a>
  <a href="http://www.facebook.com/modxcms" title="MODX Facebook">
    <img src="[[++modx.assets.url]]i/social/facebook.png" alt="Facebook" title="Like MODX on Facebook">
  </a>
  <a href="https://plus.google.com/b/111767724839610174657/111767724839610174657/posts" title="MODX on Google+">
    <img src="[[++modx.assets.url]]i/social/google-plus-black.png" alt="Google+" title="Circle MODX on Google+">
  </a>
  <a href="http://www.linkedin.com/company/modx-llc/" title="MODX on LinkedIn">
    <img src="[[++modx.assets.url]]i/social/linkedin.png" alt="LinkedIn" title="Connect MODX on LinkedIn">
  </a>
  <a href="https://github.com/modxcms" title="Fork us on GitHub">
    <img src="[[++modx.assets.url]]i/social/github.png" alt="github" title="MODX on GitHub">
  </a> 
  <a href="[[~119]]" title="Subscribe to our Feeds">
    <img src="[[++modx.assets.url]]i/social/rss.png" alt="Feeds" title="MODX RSS Feeds">
  </a> 
</div>
           </form> 
           </section>
       </div>
    </section>

       <section id="copyright">
            <div class="clearfix container">
                <p><span><a href="[[~106]]">Privacy Policy</a> | <a href="[[~107]]">Terms of Service</a> | Pixels by <a href="http://weareakta.com">AKTA Web Studio</a></span>&copy; 2005-[[Copyright]] MODX. All rights reserved. <a href="[[~104]]">Trademark Policy</a> </p>
            </div>
       </section>
       <div id="post_body"></div>
  </footer> --> ]]
<div class="overlay[[*id:ne=`320`:then=` round7`]]" id="overlay">
    <div class="contentWrap"></div>
</div>

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

<!--[if lt IE 7 ]>
<script src="[[++modx.assets.url]]js/dd_belatedpng.js?v=1"></script>
<![endif]-->

`]]
[[*beforeClose]]
<!-- remove for production--><script src="http://get.gridsetapp.com/2953/overlay/"></script>
</body>
</html>

<!-- / end move -->

<!--[[$tplOmega-2012]]-->