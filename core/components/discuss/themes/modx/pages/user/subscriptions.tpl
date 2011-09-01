
<div class="dis-profile">

	<h1 class="Category">[[%discuss.subscriptions? &user=`[[+name]]`]]</h1>
		
	<form action="[[~[[*id]]]]user/subscriptions?user=[[+id]]" method="post" class="dis-form">
							<ul class="profile">
							
   								<li>Remove All: <strong><input type="checkbox" /></strong></li>
   								
   								[[+subscriptions]]

							</ul>
    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="[[%discuss.unsubscribe]]" />
    </div>

	</form>
</div>
	

</div><!-- Close Content From Wrapper -->
	[[+bottom]]


<div id="Panel">
				<hr class="line" />
    <div class="PanelBox">

        <div class="Box">
           <h4>[[+username]]'s Profile</h4>
            <ul class="panel_info">

                <li class="Heading"><img src="[[+avatarUrl]]" alt="[[+username]]" />
            <br /><span class="small">[[+title]]</span></li>
            </ul>

        </div>
        <div class="Box">
            [[+usermenu]]
        </div>

		[[$user-sidebar]]


    </div>