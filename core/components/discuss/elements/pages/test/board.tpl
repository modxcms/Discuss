<div id="board-wrapper">
	<ul class="breadcrumbs">
		[[+trail]]
	</ul>
	<hr class="clear spacer"/>
	
	[[+subboards]]
	
	<ul class="actions right">
		[[+actionbuttons]]
	</ul>
	[[+pagination]]
	
	<hr class="clear spacer"/>
	<h3 class="[[+class]] forum_title">[[+name]]</h3>
	<div class="content_wrapper">
		<table cellspacing="0" class="discuss_table" summary="Forum [[+name]]">
			<thead>
				<tr class="th">
					<th class="topic_icon">&nbsp;</th>
					<th>Topic</th>
					<th class="stats">Stats</th>
					<th>Last Post Info</th>				
				</tr>
			</thead>
				<!-- End .cat header  -->
			<tbody>
				[[+posts]]
			</tbody>
		</table>
	</div>
	<!-- End .content_wrapper -->
	<hr class="clear spacer"/>
	
	<ul class="actions right">
		[[+actionbuttons]]
	</ul>
	[[+pagination]]
	<hr class="clear spacer"/>
	<div class="readers">
		[[+readers]]
	</div>		
</div>