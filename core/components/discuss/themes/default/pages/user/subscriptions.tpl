[[+usermenu]]
<div class="dis-profile left" style="width: 80%;">

<form class="dis-form" action="[[DiscussUrlMaker? &action=`user/subscriptions` &params=`{"user":"[[+id]]"}`]]" method="post" style="border: 0;">
    <h2>
        <span class="right" style="padding: 4px;"><input type="checkbox" class="dis-remove-all" /></span>
        [[%discuss.subscriptions? &namespace=`discuss` &topic=`user`]]
    </h2>
	<ol class="dis-board-list" style="border: 0;">	    
	    [[+subscriptions]]
	</ol>
    
    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="[[%discuss.unsubscribe]]" />
    </div>
    
</form>
</div>