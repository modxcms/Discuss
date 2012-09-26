<!-- thread/index.html -->
[[+top]]

[[+aboveThread]]
<div class="f1-f12 h-group [[+answered:notempty=`answered`]]">
    <h1 class="Category [[+locked:is=`1`:then=`locked`:else=`unlocked`]]" post="[[+id]]">
        <a href="[[+url]]" title="[[+title]]">[[+title]]<span class="idx">#[[+idx]]</span></a>
        [[+answered:notempty=`<span class="tag solved">Solved</span>`:default=``]]
    </h1>
</div>
<div class="f1-f9">
<div>
	
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

[[!$thread-sidebar-2012]]
<!--close thread/index.html -->
