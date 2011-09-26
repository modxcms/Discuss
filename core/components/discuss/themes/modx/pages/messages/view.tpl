
<div class="dis-threads">
	    [[+pagination]]


	<ul class="dis-list">
		<li><h1>[[%discuss.message? &namespace=`discuss` &topic=`post`]]: [[+title]]</h1></li>
		<li><h4 class="participants">[[%discuss.participants]]: [[+participants_usernames]]</h4></li>
		[[+posts]]
	</ul>

	    [[+pagination]]

    [[+quick_reply_form]]
</div>









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

		[[!$post-sidebar?disection=`dis-support-opt`]]
		
			<div class="Box">
			<h4>Information</h4>
			<p>[[+readers]]</p>
			</div>
</aside>