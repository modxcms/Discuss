<div class="f1-f12 h-group">
    <h1 class="Category [[+locked:is=`1`:then=`locked`:else=`unlocked`]]" post="[[+id]]">
        <a href="[[+url]]" title="[[+title]]">[[+title]]<span class="idx">#[[+idx]]</span></a>
    </h1>
</div>


<div class="f1-f9">
    <div class="a-dis-actionbuttons h-group">
        Subscribe: <a href="[[~[[*id]]]]thread/feed.xml?thread=[[+id]]">RSS</a>
                [[+actionlink_subscribe:notempty=`
                <a href="[[+actionlink_subscribe]]">By email</a>`]]
                [[+actionlink_unsubscribe:notempty=`
                <a href="[[+actionlink_unsubscribe]]">Stop emails</a>`]]
    </div>
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
    [[+pagination:notempty=`
        <div class="paginate stand-alone bottom horiz-list">[[+pagination]]</div>
    `]]
    [[+quick_reply_form]]
	[[+belowThread]]
	[[+discuss.error_panel]]
</div><!-- Close Content From Wrapper -->

[[+bottom]]

[[+sidebar]]
