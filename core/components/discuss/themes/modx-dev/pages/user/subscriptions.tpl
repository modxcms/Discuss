
<div class="dis-profile">

	<h1>[[%discuss.subscriptions? &user=`[[+name]]`]]</h1>
		
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


<aside>
				<hr class="line" />
    <div class="PanelBox">

        <div class="Box">
           <h4>[[+username]]'s Profile</h4>
            <ul class="panel_info">

                <li class="Heading"><a href="https://en.gravatar.com/site/login#your-images"><img src="[[+avatarUrl]]" alt="[[+username]]" /></a>
            <br /><span class="small">[[+title]]</span></li>
            </ul>

        </div>
        <div class="Box">
            [[+usermenu]]
        </div>

		[[$user-sidebar]]

</aside>