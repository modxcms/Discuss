
<div class="dis-profile left">

<p class="dis-breadcrumbs">
    <a href="[[~[[*id]]]]">[[++discuss.forum_title]]</a> / [[%discuss.profile]]
</p>
		<h2>[[%discuss.general_stats? &user=`[[+name]]`]]</h2>
		
	<form class="dis-form" action="[[~[[*id]]]]user/subscriptions?user=[[+id]]" method="post">
							<ul class="profile">
							
   								<li>Remove All: <strong><input type="checkbox" /></strong></li>
   								
   								[[+subscriptions]]

							</ul>


	</form>
</div>
	

</div><!-- Close Content From Wrapper -->
	[[+bottom]]


				<div id="Panel">
					<div class="PanelBox">
					
						<div class="Box GuestBox">
						   <h4>[[+name]]'s Profile</h4>
							<ul class="PanelInfo PanelCategories">

								<li class="Heading"><img src="[[+avatarUrl]]" alt="[[+username]]" />
							<br /><span class="small">[[+title]]</span></li>
							</ul>
							
						</div>
						
						<div class="Box BoxCategories">
							[[+usermenu]]


						</div>
					</div>
