<li class="dis-post">
    <div class="dis-post-header">
		<h1 class="Category">[[+title]]</h1>
        <div class="dis-post-author">
            <div class="auth-avatar">[[+author.avatar]]</div>
		</div>
            
	</div>

    
    	<div class="dis-post-content">
        	<h4 class="created">[[+author.username]] [[+createdon]]</h4>
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
