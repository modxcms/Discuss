<li class="dis-post">
    <div class="dis-post-header">
		<h1 class="Category">[[+title]]</h1>
        <div class="dis-post-author">
            <div class="dis-author">
            	<a href="[[~[[*id]]]]user/?user=[[+author.id]]" class="auth-avatar">[[+author.avatar]]</a>
			</div>
		</div>
            
	</div>

    
    	<div class="dis-post-content">
        	<h4 class="created">[[%discuss.post_author_short? &user=`[[+author.username]]` &date=`[[+createdon]]`]]</h4>
        	<div>[[+message]]</div>
            
		    <div class="dis-post-ct" id="dis-thread-ct-[[+id]]">
		        <div class="dis-post-footer">
		            <div class="dis-post-reply" id="dis-post-reply-[[+id]]"></div>
		            <div class="dis-post-attachments">
		            [[+attachments:notempty=`<ul class="dis-attachments">[[+attachments]]</ul>`]]
		            </div>
		        </div>
		    </div>
        </div>
<br class="clearfix" />
    </div>
</li>
