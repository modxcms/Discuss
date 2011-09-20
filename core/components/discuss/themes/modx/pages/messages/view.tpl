
<div class="dis-threads">



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
            <h4>Actions &amp; Info</h4>
			<p>[[+actionbuttons]]</p>
			[[+belowThreads]]
			<p>[[+readers]]</p>
			<p>[[+moderators]]</p>
	    </div>`]]

		[[!$post-sidebar?disection=`dis-support-opt`]]


</aside>