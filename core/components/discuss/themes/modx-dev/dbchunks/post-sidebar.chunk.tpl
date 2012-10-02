<aside class="sidebar twenty12 f10-f12">
[[!+discuss.user.id:is=``:then=`
    <div class="box first">
        <p><a href="[[~[[*id]]]]login" class="primary-cta login dis-action-login">Login to Post</a></p>
        <p>Don't have a MODX.com account? <a href="#">Create one</a></p>
    </div>
`]]
    <div class="panel-box">
[[!+discuss.user.id:notempty=`
        <div class="box">
            <p>[[+moderators]]</p>
        </div>
`]]
[[+disection:is=`new-message`:then=`
		<div class="Box">
			<h4>Don't Be That Guy</h4>
			<p>Be nice, respectful and patient. Inflamatory or inappropriate posts will get your post nuked and flood your life with bans and bad karma.</p>
		</div>

		<div class="Box">
		   <h4>Help Us Help You</h4>
			<p>Use a title that gives insight into your post and limit your posts to 1. Remember, this is an open source project and folks aren't paid to help you here. If you're experiencing problems, please supply adequate technical details.</p>
		</div>
`]]
        <div class="Box GuestBox">
            <div class="a-faux-btn-grp">
                <a class="a-secondary-cta l-inline-btn a-bug" href="http://tracker.modx.com">Found a bug?</a>
                <a class="a-secondary-cta l-inline-btn a-proposal" href="http://tracker.modx.com/projects/modx-proposals">Have a feature request?</a>
            </div>
            <a class="a-secondary-cta" href="[[~316]]">Buy Emergency Support <span>(Priority Support from the Source)</span></a>
        </div>
[[+disection:isnot=`new-message`:then=`
        <div class="box">
            <h4>Who’s talking</h4>
            <p>Posted in this thread:<br />
            	[[+participants_usernames]]
            </p>
            <p>[[+readers]]</p>
        </div>
]]
    </div>
    <!-- close .panel-box -->
</aside>
<!-- close aside -->
