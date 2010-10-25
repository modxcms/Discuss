<tr class="[[+rowClass]]" id="board-[[+id]]">
	<td class="altrow board_icon">
		<!--
		<img src="/assets/components/remark/themes/default/images/comment-32.png" alt="New Replies"/>
		-->
	</td>					
	<td class="infos">
		<a class="title" href="[[~[[++discuss.board_resource]]? &board=`[[+id]]`]]">
			[[+name]]
		</a>
		<p>[[+description]]</p>
		[[+subforums:notempty=`
		<ul class="subforums">
			[[+subforums]]
		</ul>
		`]]
	</td>
	<td class="altrow forum_stats">
		<ul class="overall">
			<li>
				[[+num_topics]] 
				<span class="ret">Topics</span>
			</li>
			<li class="sep">+</li>
			<li class="last">
				[[+num_replies]]
				<span class="ret">Replies</span>
			</li>			
		</ul>
	</td>
	<td class="last_post">
		<ul>
			<li class="date">
				Last on [[+last_post_createdon:empty=`-- --`]]
			</li>
			<li>
				Posted by <a href="#">[[+last_post_username:empty=`--`]]</a>&nbsp;
			</li>
		</ul>
	</td>	
</tr>