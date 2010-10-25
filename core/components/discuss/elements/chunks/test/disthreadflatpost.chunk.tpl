<div class="post_content_wrapper">
	<div class="post_block[[+firstClass]]" id="post-[[+id]]">
		<h3 class="clearfix">
			<span class="post_id">
				<a href="[[~[[*id]]]]?view=topic&amp;tid=[[+topicid]]#post-[[+id]]" rel="bookmark" title="Link to post #post-[[+id]]">#[[+idx]]</a>
			</span>
			<span class="post_date">
				Posted on <abbr class="published" title="[[+createdon:strtotime:date=`[[++discuss.date_format]]`]]">[[+createdon:strtotime:date=`[[++discuss.date_format]]`]]</abbr>
			</span>
		</h3>
		<!-- End h3 - Post meta -->
		
		<div class="author_info">
			<ul class="user_details">
				<li class="avatar">
					<a href="#" title="View Profile">
						<!-- @TODO change this image for user -->
						[[+author_avatar]]
					</a>
				</li>
				<li class="user_name">
					<strong>[[+username]]</strong>
				</li>
			</ul>
			<!-- End .user details -->
			
			<!-- @TODO get Fields from user profile with custom snippet -->
			<ul class="user_fields">
				<li>
					<span class="ft">Group:</span>
					<span class="fc"><strong>Administrator</strong></span>
				</li>
				<li>
					<span class="ft">[[%discuss.posts]]:</span>
					<span class="fc">[[+author_posts]]</span>
				</li>
				<li>
					<span class="ft">Joined:</span>
					<span class="fc">01 September 2010</span>
				</li>
			</ul>

		</div>
		<!-- End .author_infos -->
		
		<div class="post_body">
			[[+message]]
			[[If? &subject=`[[+attachments]]`
              &operator=`!empty`
              &then=`
              <ul class="attachments">
              [[+attachments]]
              </ul>`
            ]]
		</div>
	
		<div class="post_footer">
			[[+action_reply:notempty=`
			<ul class="controls">
				<li>
					[[+action_reply]]
				</li>
				<li>
					[[+action_modify]]
				</li>
				<li>
					[[+action_remove]]
				</li>
			</ul>
			`]]
		</div>
	</div>
	<!-- End .post_block -->
</div>
<!-- End .forum_content_wrapper -->