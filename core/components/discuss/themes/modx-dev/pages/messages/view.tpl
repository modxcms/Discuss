<div class="f1-f12 h-group">
    <h1 class="Category [[+locked:is=`1`:then=`locked`:else=`unlocked`]]" post="[[+id]]">
        <a href="[[+url]]" title="[[+title]]">[[+title]] WHAT</a>
    </h1>
    <p class="pm-participants">[[%discuss.message.participants]] [[+participants_usernames]]</p>
</div>


<div class="f1-f9">
    <div class="a-dis-actionbuttons h-group">
        Subscribe: <a href="[[~[[*id]]]]thread/feed.xml?thread=[[+id]]">RSS</a>
                [[+actionlink_subscribe:notempty=`
                <a href="[[+actionlink_subscribe]]">By email</a>`]]
                [[+actionlink_unsubscribe:notempty=`
                <a href="[[+actionlink_unsubscribe]]">Stop emails</a>`]]
    </div>
    [[+pagination:default=``]]
</div>

<div class="f1-f9">
    <header class="dis-cat-header dark-gradient group-fix sticky-bar top">
        [[+pagination:default=``]]
        [[!+discuss.user.id:notempty=`
            <div class="post-box h-group">
                <a class="reply Button" href="[[+actionlink_reply]]">Reply to thread</a>
                <a class="read" href="[[+actionlink_unread]]">Mark as unread</a>
                [[+moderators]]
            </div>
        `]]
    </header>
	<ul class="dis-list h-group">
        [[+posts]]
    </ul>
    [[+pagination:notempty=`<nav class="paginate stand-alone bottom horiz-list"> [[+pagination]]</nav>`]]
    [[+quick_reply_form]]
	[[+belowThread]]
	[[+discuss.error_panel]]
</div><!-- Close Content From Wrapper -->

[[+bottom]]

[[+sidebar]]
