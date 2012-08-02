<!-- thread/index.html -->
[[+top]]

[[+aboveThread]]
<div id="Content">
<div>
	<h1 class="Category [[+locked:is=`1`:then=`locked`:else=`unlocked`]]" post="[[+id]]"><a href="[[+url]]">[[+title]]<span class="idx">#[[+idx]]</span></a></h1>
	[[+pagination:notempty=`<div class="paginate stand-alone top horiz-list"> [[+pagination]]</div>`]]
	<ul class="dis-list group-fix">
        [[+posts]]
    </ul>
	[[+pagination:notempty=`<div class="paginate stand-alone bottom horiz-list"> [[+pagination]]</div>`]]
	[[$thread-login-post]]
    [[+quick_reply_form]]

    <br class="clearfix" />
</div>

	[[+belowThread]]
	<br class="clearfix" />
	[[+discuss.error_panel]]
</div><!-- Close Content From Wrapper -->

[[+bottom]]

[[$thread-sidebar-2012]]
	[[- Replaced by new sidebar
	<!--
	<aside>
	    <hr class="line" />
	    <div class="PanelBox">
	        [[!+discuss.user.id:notempty=`<div class="Box">
	            <h4>Actions</h4>
				<p>[[+actionbuttons]]</p>
				<p>Subscribe:
				[[+subscribeUrl:notempty=`<a href="[[+subscribeUrl]]">By email</a>`]]
				[[+unsubscribeUrl:notempty=`<a href="[[+unsubscribeUrl]]">Stop emails</a>`]]</p>
				<p>[[+moderators]]</p>
		    </div>`]]
	        [[!+discuss.user.id:is=``:then=`<div class="Box">
			    <h4>Actions</h4>
				<p><a href="[[~[[*id]]]]login" class="Button">Login to Post</a></p>
			</div>`]]
			[[!$post-sidebar?disection=`dis-support-opt`]]
				<div class="Box">
				<h4>Information</h4>
				<p>Posted in this thread:<br />[[+participants_usernames]]</p>
				<p>[[+readers]]</p>
				</div>
	</aside>
	</div> --> ]]
<!--close thread/index.html -->