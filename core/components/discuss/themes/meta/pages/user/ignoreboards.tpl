
<div class="dis-profile left">

	<ul class="DataList CategoryList CategoryListWithHeadings">
	
		<li class="Item CategoryHeading Depth1">
	    <div class="ItemContent Category">Ignore Boards</div>
	    </li>
	</ul>
		
<form action="[[~[[*id]]]]user/ignoreboards?user=[[+id]]" method="post">

							<ul class="profile">
   								
     					       [[+boards]]

							</ul>

<br class="clearfix" />
    <div class="dis-form-buttons">
    Ignore All: <input type="checkbox" class="dis-ignore-all" />
    <input type="submit" class="dis-action-btn" value="Update" />
    </div>

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
