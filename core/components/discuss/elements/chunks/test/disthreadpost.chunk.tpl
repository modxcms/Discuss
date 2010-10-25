<li class="post depth-[[+depth]]" id="post-[[+id]]"> 
	<div class="post-wrapper [[+alt]]">
		<h3 class="post-title">[[+title]]</h3>
		<div class="author_infos">
			<img width="40" height="40" src="/assets/images/[[+author:userinfo=`username`]].jpg" alt="" />
			<cite>[[+author:userinfo=`username`]]</cite>
			<span class="postedon">[[+createdon]]</span>
			<div class="post-report">
				<a href="javascript:void([[+id]]);">[[%discuss.report_to_mod]]</a>
				<a href="javascript:void([[+id]]);">[[+ip]]</a>
			</div>
		</div>
		
		[[+depth:gt=`0`:then=`<div class="tree">&nbsp;</div>`]]
		<div class="content-wrap">
			
								
			<div class="post-body"> 
				[[+content]]
				[[+attachments:notempty=`
					<ul class="post-attachments">
						[[+attachments]]
					</ul>
				`]]
				[[+author_signature:notempty=`
					<p class="author_signature">
						[[+author_signature]]
					</p>
				`]]			
			</div> 
			
			<div class="post-footer">
				
				<div class="post-options">
					[[+action_remove]]
					[[+action_modify]]
					[[+action_reply]]	
					<a href="[[~[[++discuss.reply_post_resource]]? &post=`[[+id]]` &quote=`1`]]">Quote</a>
				</div>
			</div>
		</div>
		<!-- End .content-wrap -->
	</div>
	[[+children:ifnotempty=`<ol class="children">[[+children]]</ol>`]]
</li>