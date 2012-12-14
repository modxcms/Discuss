
<div class="dis-profile">

	<h1>Ignore Boards</h1>

	<form action="[[~[[*id]]]]user/ignoreboards?user=[[+id]]" method="post" class="dis-form">

		<ul class="ignore">
			[[+boards]]
		</ul>
			
		<label class="dis-cb"><input type="checkbox" class="dis-ignore-all" /><strong>Ignore All</strong></label>

		<br class="clearfix" />

	    <div class="dis-form-buttons">
	    	<input type="submit" name="submit" value="Update" />
	    </div>

	</form>
	
</div>
[[+sidebar]]

</div><!-- Close Content From Wrapper -->
	[[+bottom]]


