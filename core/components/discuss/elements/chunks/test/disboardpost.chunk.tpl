<tr class="[[+rowClass]]" id="board-[[+id]]">
	<td class="altrow board_icon">
		<!--
		<img src="/assets/components/remark/themes/default/images/comment-32.png" alt="New Replies"/>
		-->
	</td>					
	<td class="infos">
		[[+sticky:if=`[[+sticky]]`:eq=`1`:then=`<span class="pinned">pinned</span>`:else=``]]
		<a class="thread_title" href="[[~[[++discuss.thread_resource]]? &thread=`[[+id]]`]]">
			[[+title]]
		</a>
		<cite>
		by <a href="#">[[+author:userinfo=`username`]]</a>&nbsp;	
		</cite>
		<!-- Add the ability to have a topic description in description table -->
		[[+forumdesc]]
	</td>
	<td class="altrow stats">
		<ul>
			<li class="board_replies">
				<span>
					<a href="[[~[[*id]]]]">[[+replies]]</a>
				</span> Replies
			</li>
			<li class="views">[[+views]] Views</li>
		</ul>
	</td>
	<td class="last_post">
		<ul>
			<li class="date">
				<a href="[[~[[++discuss.thread_resource]]? &thread=`[[+id]]`]]#post-[[+latest.id]]" title="Go to last post">[[+createdon]]</a>
			</li>
			<li>
				Posted by [[+lastest]]
			</li>
		</ul>
	</td>	
</tr>