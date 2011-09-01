<li class="[[+class]]" id="dis-post-[[+id]]">
    <div class="dis-post">
        <div class="dis-post-author" id="dis-post-author-[[+id]]">
            <div class="dis-author">
            	<a href="[[~[[*id]]]]user/?user=[[+author.id]]" class="auth-avatar">[[+author.avatar]]</a>
            	<div class="auth-count"><div class="post_icon" title="[[+author.posts]] Posts">[[+author.posts]]</div><!--<div class="badge_icon" title="[[+author.modxp]] XP">3</div>--></div>
                <div class="avatarHover">
						[[+author.email]]
						[[+author.group_badge:notempty=`<img class="group-badge" src="[[+author.group_badge]]" alt="" title="[[+author.group_name]]" />`]]
						[[+author.title:notempty=`<em class="dis-author-title"> - [[+author.title]]</em>`]]
					
					<a href="[[~[[*id]]]]post/track?ip=[[+ip]]" class="dis-post-track">[[+ip]]</a>
						
		            <div class="dis-hidden dis-sig-ct dis-sig-ct-[[+id]]">
		                [[+author.signature:notempty=`<div class="dis-signature">[[+author.signature]]</div>`]]
		            </div>
                </div>
            </div>
            
        </div>

    
    	<div class="dis-post-content">
        	<h4 class="created">[[+author.username_link]] <a class="normal-type" href="[[+url]]">Reply #[[+idx]], [[+createdon:ago]]</a></h4>
        	<!--<a href="" class="dis-fav">Favorite</a>-->
        	[[+link_mark_as_answer]]
        	<a href="#" class="quick-reply hide"><span>Reply</span></a>
            <div class="dis-actions"><div><ul>[[+actions]]<li><a href="[[+url]]">Link to this post<span class="idx">#[[+idx]]</span></a>
</li><li>[[+report_link]]</li></ul></div></div>
        	<div>[[+content]]</div>
            
		    <div class="dis-post-ct" id="dis-thread-ct-[[+id]]">
		
		        <div class="dis-post-footer">
		            <div class="dis-post-attachments">
		            [[+attachments:notempty=`<ul class="dis-attachments">[[+attachments]]</ul>`]]
		            </div>
		            <div class="dis-post-ip">
		                [[+editedby:is=`0`:then=``:else=`<span class="dis-post-editedon">Edited [[+editedon:ago]] by <a href="[[~[[*id]]]]user?user=[[+editedby]]">[[+editedby.username]]</a></span>`]]
		                
		            </div>
		        </div>
		         	<br class="clearfix" />
		        [[+children:notempty=`<ul class="dis-list [[+children_class]]">[[+children]]</ul>`]]
		    </div>
		    
        </div>


    </div>
<br class="clearfix" />

</li>
