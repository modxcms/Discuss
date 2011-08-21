
<div class="dis-profile left">

	<h1 class="Category">[[%discuss.general_stats? &user=`[[+name]]`]]</h1>

							<ul class="profile">
							
   								<li>[[%discuss.joined]]: <strong>[[+confirmedon:strtotime:date=`%b %d, %Y %I:%M %p`]]</strong></li>
   								
   								<li>[[%discuss.post_count]]: <strong>[[+posts]]</strong></li>
   								
   								<li>[[%discuss.threads_started]]: <strong>[[+topics]]</strong></li>
   								
   								<li>[[%discuss.replies]]: <strong>[[+replies]]</strong></li>
								
								<li>[[%discuss.last_login]]: <strong>[[+last_login:strtotime:date=`%b %d, %Y %I:%M %p`]]</strong></li>
								
								<li>[[%discuss.last_active]]: <strong>[[+age]]</strong></li>
								
								<li>[[%discuss.location]]: <strong>[[+last_active:strtotime:date=`%b %d, %Y %I:%M %p`]]</strong></li>

							</ul>




</div>
	

</div><!-- Close Content From Wrapper -->
	[[+bottom]]


<div id="Panel">
				<hr class="line" />
    <div class="PanelBox">

        <div class="Box GuestBox">
           <h4>[[+username]]'s Profile</h4>
            <ul class="PanelInfo PanelCategories">

                <li class="Heading"><img src="[[+avatarUrl]]" alt="[[+username]]" />
            <br /><span class="small">[[+title]]</span></li>
            </ul>

        </div>
        <div class="Box BoxCategories">
            [[+usermenu]]
        </div>

		[[$user-sidebar]]


    </div>