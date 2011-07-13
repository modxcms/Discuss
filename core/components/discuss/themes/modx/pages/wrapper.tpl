
<div id="header">
		<a class="hidden" href="#main">Skip to content</a>
		<header class="container">
			<nav id="global">
			[[+discuss.user.id:notempty=`<a href="[[~[[*id]]]]user/?user=[[+discuss.user.id]]">[[+modx.user.username]]</a>`]]
			<a href="http://modx.com/">modx.com</a>
			<a href="[[~[[*id]]]]search">Search</a>
                    [[+discuss.user.id:notempty=`<a href="[[~[[*id]]]]user/?user=[[+discuss.user.id]]">Profile</a>`]]
                    [[+discuss.user.id:notempty=`<a href="[[~[[*id]]]]messages/">Messages ([[+discuss.user.unread_messages]])</a>`]]
                    [[+discuss.user.id:is=``:then=`<a href="[[~[[*id]]]]register">Register</a>`]]
                    [[+discuss.authLink]]

				<a class="clearfix" href="http://modx.com/search/">Search</a>
			</nav>
			<nav id="user">
			<a title="Open Source PHP Content Management System, Framework, Platform and More" id="logo" class="ir" href="/revolution/forums/">MODX Open Source Content Management System, Framework, Platform and More.</a>
				<div id="search">
					<form accept-charset="utf-8" method="get" action="http://modx.com/search-results/">
						<label class="hidden" for="search_form_input">Search</label>

						<input title="Start typing and hit ENTER" value="" name="search" placeholder="Search keyphrase..." id="search_form_input" class="hasPlaceholder" type="text">
						<input value="Go" type="submit">
					</form>  
				</div>
			</nav>
		</header>
</div>



	
<!-- end header -->

<div>
	<div id="section_wrap">
		<header class="container">
			<nav id="section">
				<ul>
					[[+discuss.user.id:notempty=`<li class="first level1"><a href="[[~[[*id]]]]thread/unread" class="last level1"><span class="Title">View Unread Posts</span> All Discussion Categories</a></li>`]]
					[[+discuss.user.id:notempty=`<li class="last level1"><a href="[[~[[*id]]]]thread/unread_last_visit" class="last level1"><span class="Title">View New</span>Posts Since Last Visit</a></li>`]]
					[[+discuss.user.id:notempty=`<li class="last level1"><a href="[[~[[*id]]]]thread/new_replies_to_posts" class="last level1"><span class="Title">[[%discuss.new_replies_to_posts]]</span>[[%discuss.new_replies_to_posts]]</a></li>`]]
					[[+discuss.user.id:notempty=`<li class="last level1"><a href="[[~[[*id]]]]thread/recent" class="last level1"><span class="Title">View Recent Posts</span>My Latest Posts</a></li>`]]



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