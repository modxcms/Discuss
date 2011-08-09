
[[+top]]

<div class="dis-profile left">

<form action="[[~[[*id]]]]user/?user=[[+id]]" method="post" id="dis-user-edit-form">

	<h1 class="Category">[[+name]]</h1>


							<ul class="profile">
   								<li>[[%discuss.name? &namespace=`discuss` &topic=`user`]]: <strong>[[+name_first]] [[+name_last]]</strong></li>
   								<li>[[%discuss.posts]]: <strong>[[+posts]]</strong></li>
   								<li>[[%discuss.groups]]: <strong>[[+groups]]</strong></li>
   								[[+ip:notempty=`<li>[[%discuss.ip? &namespace=`discuss` &topic=`web`]]: <strong>[[+ip]]</strong></li>`]]
							
   								<li>[[%discuss.date_registered]]: <strong>[[+createdon:strtotime:date=`%b %d, %Y`]]</strong></li>
   								[[+last_active:notempty=`<li>[[%discuss.last_online]]: <strong>[[+last_active]]</strong></li>
   								<li>[[%discuss.last_reading]]: <strong><a href="[[+last_post_url]]">[[+lastThread.title]]</a></strong></li>`]]
   								[[+email:notempty=`<li>[[%discuss.email]]: <strong><a href="mailto:[[+email]]">[[+email]]</a></strong></li>`]]
   								
   								<li>[[%discuss.website]]: <strong>[[+website]]</strong></li>
								
								<li>[[%discuss.gender]]: <strong>[[+gender]]</strong></li>
								
								<li>[[%discuss.age]]: <strong>[[+age]]</strong></li>
								
								<li>[[%discuss.location]]: <strong>[[+location]]</strong></li>

							</ul>



</form>








</div>
	

</div><!-- Close Content From Wrapper -->
	[[+bottom]]


				<div id="Panel">
				<hr class="line" />
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
