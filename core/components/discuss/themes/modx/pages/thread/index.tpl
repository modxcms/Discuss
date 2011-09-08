[[+top]]

[[+aboveThread]]



<div>
	<ul class="dis-list">
		<li><h1 class="Category [[+locked:is=`1`:then=`locked`:else=`unlocked`]]" post="[[+id]]"><a href="[[+url]]">[[+title]]<span class="idx">#[[+idx]]</span></a></h1></li>
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


<div id="Panel">
    <hr class="line" />
    <div class="PanelBox">
        [[!+discuss.user.id:notempty=`<div class="Box">
            <h4>Actions &amp; Info</h4>
			<p>[[+actionbuttons]]</p>
			[[+belowThreads]]
			<p>[[+readers]]</p>
			<p>[[+moderators]]</p>
	    </div>`]]
        [[!+discuss.user.id:is=``:then=`<div class="Box">
		    <h4>Actions &amp; Info</h4>
			<p><a href="[[~[[*id]]]]login" class="Button">Login to Post</a></p>
		</div>`]]
		
		[[!$post-sidebar?disection=`dis-support-opt`]]
		

    </div>