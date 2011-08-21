
<div class="dis-profile left">

	<h1 class="Category">Ignore Boards</h1>
		
<form action="[[~[[*id]]]]user/ignoreboards?user=[[+id]]" method="post">

							<ul class="profile">
   								
     					       [[+boards]]

							</ul>

<br class="clearfix" />
    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="Update" />
    <label class="dis-cb"><input type="checkbox" class="dis-ignore-all" />Ignore All</label>
    </div>

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