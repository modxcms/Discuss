[[+top]]

[[+aboveThread]]
	    [[+pagination]]

<div>
	<ul class="dis-list">
		<li><h1 class="Category [[+locked:is=`1`:then=`locked`:else=`unlocked`]]" post="[[+id]]"><a href="[[+url]]">[[+title]]<span class="idx">#[[+idx]]</span></a></h1></li>
		<li><h4 class="participants">[[%discuss.participants]]: [[+participants_usernames]]</h4></li>
        [[+posts]]
    </ul>
	    [[+pagination]]
	
    [[+quick_reply_form]]

    <br class="clearfix" />
</div>

	[[+belowThread]]
	
	<br class="clearfix" />
	[[+discuss.error_panel]]
	
</div><!-- Close Content From Wrapper -->

[[+bottom]]


<aside>
    <hr class="line" />
    <div class="PanelBox">
        [[!+discuss.user.id:notempty=`<div class="Box">
            <h4>Actions</h4>
			<p>[[+actionbuttons]]</p>

			<p>[[+moderators]]</p>
	    </div>`]]
        [[!+discuss.user.id:is=``:then=`<div class="Box">
		    <h4>Actions</h4>
			<p><a href="[[~[[*id]]]]login" class="Button">Login to Post</a></p>
		</div>`]]
		
		[[!$post-sidebar?disection=`dis-support-opt`]]
		
			<div class="Box">
			<h4>Information</h4>
			<p>[[+readers]]</p>
			</div>
</aside>