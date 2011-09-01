
<div class="dis-profile">

	<h1 class="Category">Ignore Boards</h1>
		
<form action="[[~[[*id]]]]user/ignoreboards?user=[[+id]]" method="post" class="dis-form">

							<ul>
   								
     					       [[+boards]]

							</ul>
<label class="dis-cb"><input type="checkbox" class="dis-ignore-all" /><strong>Ignore All</strong></label>
<br class="clearfix" />

    
    <div class="dis-form-buttons">
    <input type="submit" value="Update" />
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



    </div>