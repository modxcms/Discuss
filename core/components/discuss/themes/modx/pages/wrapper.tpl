<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <!--[if IE]><![endif]-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<base href="[[!++site_url]]">
    <title>MODX :: [[!+discuss.pagetitle]]</title>
    <meta name="title" content="Test Page">    
    <meta name="author" content="MODX Systems, LLC">

	[[*cssjs]]
    <!-- TypeKit -->
    <script src="http://use.typekit.com/zub5doo.js"></script>
    <script type="text/javascript">try{Typekit.load();}catch(e){}</script>
</head>
<body>
<div id="header">
	<a class="hidden" href="#main">Skip to content</a>
	<header class="container">
		<nav id="global2">
			<!--<a href="">Revolution</a>-->
			<a href="http://modx.com/revolution/">Revolution</a>
			<a href="http://modx.com/evolution/">Evolution</a>
			<a href="http://modx.com/partners/">Partners</a>
			<a href="http://modx.com/community/">Community</a>
		</nav>

	
		<nav id="global">
			[[+discuss.user.id:is=``:then=`<a href="[[~[[*id]]]]login">Login</a> | <a href="[[~[[*id]]]]register">Register</a>`]]
			[[+discuss.user.id:notempty=`Welcome, <a href="[[~[[*id]]]]user/?user=[[+discuss.user.id]]">[[+modx.user.username]]</a> | <a href="[[~[[*id]]]]logout">Logout</a>`]]
		</nav>
		

          
          	
		<nav id="user">
			<a title="Open Source PHP Content Management System, Framework, Platform and More" id="logo" class="ir" href="[[~[[*id]]]]">MODX Open Source Content Management System, Framework, Platform and More.</a>
		</nav>

			<nav id="logo_search">
				<div id="search">
		            <div class="links">
		                <a href="http://modx.com/partners/solution/">Find a Partner</a>  <span class="ir">|</span>
		                <a href="http://modx.com/partners/hosting-saas/">Hosts + SaaS</a> <span class="ir">|</span>
		                <a href="http://modx.com/services/jobs/">Jobs</a> <span class="ir">|</span>
		                <a href="http://modx.com/community/wall-of-fame/support-modx/">Donate</a>
		            </div>
		
		                <form action="[[~[[*id]]]]search" method="get" accept-charset="utf-8">
							<label for="search_form_input" class="hidden">Search</label>
							<input id="search_form_input" placeholder="Search keyphrase..." name="s" value="" title="Start typing and hit ENTER" type="text">
							<input value="Go" type="submit">
						</form>   
		        </div><!-- #search -->
			</nav>
	</header>
</div>



	
<!-- end header -->

<div>
	<div id="section_wrap">
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
		
[[+trail]]

	</div>
	
		<div id="Frame">
		
			<div id="Body">
			
				<div id="Content">
					[[+content]]
					
				<!-- Close Content Inside home.tpl -->
			
				</div>
			</div>
			
	    </div>
	    
    <div class="clearfix">&nbsp;</div>
							

</div>
<footer>
      <a href="[[!AnchorMan? &anchor=`header`]]" id="top" class="ir">Back to Top</a>
       <nav id="destinations">
          <div class="container">
              <section class="company first">
                  <h3><a href="[[~12]]">Company</a></h3>
                  <ul>
                      <li><a href="[[~113]]">Blog</a></li>
                      <li><a href="[[~25]]">Contact</a></li>
                      <li><a href="[[~18]]">Media Center</a></li>
                      <li><a href="[[~27]]">Services</a></li>
                      <li><a href="[[~11]]">Partners</a></li>
                  </ul>
              </section>
              <section class="support">
                  <h3><a href="[[~6]]">Support</a></h3>
                  <ul>
                      <li><a href="[[~10]]">Commercial Support</a></li>
                      <li><a href="[[~100]]">Community Support</a></li>
                      <li><a href="[[~16]]">Documentation</a></li>
                      <li><a href="[[~102]]">Bugs &amp; Suggestions</a></li>
                  </ul>
              </section>
              <section class="developer">
                  <h3><a href="[[~5]]">Developer</a></h3>
                  <ul>
                    <li><a href="[[~133]]">Get the Source</a></li>
                    <li><a href="[[~129]]">Contribute</a></li>
                    <li><a href="[[~131]]">API Documentation</a></li>
                    <li><a href="[[~17]]">Documentation</a></li>
                    <li><a href="[[~29]]">Issue Tracker</a></li>
                  </ul>
              </section>
              <section class="community">
                  <h3><a href="[[~4]]">Community</a></h3>
                  <ul>
                      <li><a href="[[~13]]">Forums</a></li>
                      <li><a href="[[~110]]">Wall of Fame</a></li>
                      <li><a href="[[~109]]">Donate to MODX</a></li>
                      <li><a href="[[~19]]">Spread MODX</a></li>
                      <li><a href="[[~129]]">Contribute</a></li>
                  </ul>
              </section>
              <section class="learn last clearfix">
                  <h3><a href="[[~7]]">Learn</a></h3>
                  <ul>
                      <li><a href="[[~17]]">Documentation</a></li>
                      <li><a href="[[~32]]">Solutions</a></li>
                      <li><a href="[[~108]]">Made in MODX</a></li>
                      <li><a href="[[~31]]">Books</a></li>
                  </ul>
              </section>
            </div> <!-- .container -->
       </nav>
       <section id="subscribe">
           <div class="container clearfix">
              <form id="newsletter" action="http://modxcms.list-manage.com/subscribe/post" method="post">
                  <h3>Subscribe to Our Newsletter</h3>
                  <input type="hidden" name="u" value="08b25a8de68a29fe03a483720" />
                  <input type="hidden" name="id" value="848cf40420" />
                  <input type="hidden" name="source" value="www_[[*id]]" id="source">
                  <input type="hidden" name="MERGE7" value="[[~[[*id]]? &scheme=`full`]]" id="MERGE7">
                  <div class="clearfix">
                      <label for="MERGE0" class="hidden">Your email</label>
                      <input type="text" placeholder="you@example.com" required id="MERGE0" name="MERGE0" value="" class="textbox" />
                      <input type="submit" name="Submit" value="Sign up" />
                  </div>
                  <p><a href="[[~458]]">Read the previous issues</a></p>
              </form>
              <div id="sponsors" class="clearfix">
                  <h3>Sponsors</h3>
                  <a href="[[~55]]" class="ir last" id="sponsor_modx">Sponsor MODX</a>
                  <a href="http://www.microsoft.com/web/websitespark/" class="ir" id="mswss">Microsoft Websitespark</a>
                  <a href="http://firehost.com/" class="ir" id="firehost" title="Firehost: Secure Cloud Hosting">Firehost: Secure Cloud Hosting</a>
              </div>
          </div>
       </section>
       <section id="copyright">
            <div class="clearfix container">
                <p><span><a href="[[~106]]">Privacy Policy</a> | <a href="[[~107]]">Terms of Service</a> | Pixels by <a href="http://weareakta.com">AKTA Web Studio</a></span>&copy; 2005-2011 MODX. All rights reserved. <a href="[[~104]]">Trademark Policy</a> </p>
            </div>
       </section>
       <div id="post_body"></div>
  </footer>
<div class="overlay round7" id="overlay">
<div class="contentWrap"></div>
</div>
</body>
</html>