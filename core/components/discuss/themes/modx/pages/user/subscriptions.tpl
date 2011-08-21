
<div class="dis-profile left">

	<h1 class="Category">[[%discuss.subscriptions? &user=`[[+name]]`]]</h1>
		
	<form action="[[~[[*id]]]]user/subscriptions?user=[[+id]]" method="post">
							<ul class="profile">
							
   								<li>Remove All: <strong><input type="checkbox" /></strong></li>
   								
   								[[+subscriptions]]

							</ul>


	</form>
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