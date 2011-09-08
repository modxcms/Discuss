

[[<h4 class="right">[[%discuss.participants]]: [[+participants_usernames]]</h4>]]

<div class="dis-threads">



	<ul class="dis-list">
		<li><h1>[[%discuss.message? &namespace=`discuss` &topic=`post`]]: [[+title]]</h1></li>
		[[+posts]]
	</ul>

    <div class="dis-pagination"><ul>[[+pagination]]</ul></div>

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