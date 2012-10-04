
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
	
[[+sidebar]]
</div><!-- Close Content From Wrapper -->
[[+bottom]]
