<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <!--[if IE]><![endif]-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<base href="[[!++site_url]]">
    <title>Discuss Test</title>
    <meta name="title" content="Test Page">    
    <meta name="author" content="MODX Systems, LLC">

	[[*cssjs]]

</head>
<body>
<div id="header">
	<a class="hidden" href="#main">Skip to content</a>
	<header class="container">
		<nav id="global2">
			<a href="">Revolution</a>
			<a href="">Evolution</a>
			<a href="">Partners</a>
			<a href="">International</a>
		</nav>

	
		<nav id="global">
			<a href="http://modx.com/">MODX.com</a>
            [[+discuss.user.id:notempty=`<a href="[[~[[*id]]]]user/?user=[[+discuss.user.id]]">Profile</a>`]]
            [[+discuss.authLink]]

            [[+discuss.user.id:is=``:then=`<a href="[[~[[*id]]]]register">Register</a>`]]
		</nav>
		

          
          	
		<nav id="user">
			<a title="Open Source PHP Content Management System, Framework, Platform and More" id="logo" class="ir" href="/revolution/forums/">MODX Open Source Content Management System, Framework, Platform and More.</a>
		</nav>

			<nav id="logo_search">
				<div id="search">
		            <div class="links">
		                <a href="partners/solution/">Find a Partner</a>  <span class="ir">|</span>
		                <a href="partners/hosting-saas/">Hosts + SaaS</a> <span class="ir">|</span> 
		                <a href="services/jobs/">Jobs</a> <span class="ir">|</span> 
		                <a href="community/wall-of-fame/support-modx/">Donate</a>
		            </div>
		
		                <form action="search-results/" method="get" accept-charset="utf-8">
							<label for="search_form_input" class="hidden">Search</label>
							<input id="search_form_input" placeholder="Search keyphrase..." name="search" value="" title="Start typing and hit ENTER" type="text">
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
				
            		<li class="level1"><a href="[[~[[*id]]]]login" class="first level1"><span class="Title">Login</span>Click Here to Login</a></li>`]]
				
				
					[[+discuss.user.id:notempty=`<li class="first level1 parent">
						<a href="[[~[[*id]]]]thread/unread" class="first level1 parent"><span class="Title">View Unread Posts</span> All Discussion Categories</a>
						<ul class="inner">
							<li class="first level2 parent"><a href="[[~[[*id]]]]thread/unread_last_visit" class=""><span class="Title">View New</span>Posts Since Last Visit</a></li>
							<li class="first level2 parent"><a href="[[~[[*id]]]]thread/new_replies_to_posts" class=""><span class="Title">New Replies</span>[[%discuss.new_replies_to_posts]]</a></li>
							<li class="first level2 parent"><a href="[[~[[*id]]]]thread/recent" class=""><span class="Title">Recent Posts</span>My Latest Posts</a></li>
						</ul>
					</li>
					
					<li class="level1">
					<a href="[[~[[*id]]]]messages/" class="level1"><span class="Title">Private Discussions</span> All Private Messages</a>
					</li>`]]
				</ul>   
			</nav>
		</header>
	</div>
	
		<div id="Frame">
		
			<div id="Body">
			
				<div id="Content">
					[[+content]]
					
				<!-- Close Content Inside home.tpl -->
			
			
			</div>
			
	    </div>
	    
    <div class="clear">&nbsp;</div>
							

</div>

</body>
</html>