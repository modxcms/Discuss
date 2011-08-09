[[+top]]

[[+aboveThread]]

	<h1 class="Category[[+locked:is=`1`:then=` locked`]]">[[+title]]</h1>

	<div>
		<ol class="dis-board-thread">
			[[+posts]]
			<li>[[+pagination]]</li>
		</ol>

		<form action="[[~[[*id]]]]thread/reply" method="post" class="dis-form[[+locked:notempty=` locked`]]" id="dis-quick-reply-form" enctype="multipart/form-data">

            <input type="hidden" id="dis-quick-reply-board" name="board" value="[[+board]]" />
            <input type="hidden" id="dis-quick-reply-thread" name="thread" value="[[+id]]" />
            <input type="hidden" id="dis-quick-reply-post" name="post" value="[[+lastPost.id]]" />

            <input type="hidden" name="title" id="dis-quick-reply-title" value="Re: [[+title]]" />

            <div class="wysi-buttons">[[+reply_buttons]]</div>

            <textarea name="message" id="dis-quick-reply-message" rows="7">[[+message]]</textarea>

            [[+attachment_fields]]
            <br class="clearfix" />

            [[+locked_cb]]
            [[+sticky_cb]]
            <label class="dis-cb"><input type="checkbox" name="notify" value="1" />Notify of Replies</label>

            <br class="clearfix" />

            <div class="dis-form-buttons">
                <input type="submit" name="dis-post-reply" value="Quick Reply" />
                <input type="button" name="dis-post-reply-preview" class="dis-preview" value="Preview Reply" />
            </div>
            
        </form>
        
        <div id="dis-reply-post-preview"></div>

	</div>


	<div class="dis-thread-actions">[[+threadactionbuttons]]</div>

	
	[[+belowThread]]
	
	<br class="clearfix" />
	[[+discuss.error_panel]]
	
</div><!-- Close Content From Wrapper -->

[[+bottom]]



<div id="Panel">
				<hr class="line" />
    <div class="PanelBox">

        <div class="Box GuestBox">
            <h4>Info and Actions</h4>
            <p>[[+actionbuttons]]</p>

            <p>[[+subscribed:is=`1`:then=`<a href="[[+unsubscribeUrl]]">Unsubscribe</a>`:else=`<a href="[[+subscribeUrl]]">Subscribe</a>`]]</p>
            <p>[[+views]] total views.</p>
            <p>[[+readers]]</p>
            <p><a href="[[~[[*id]]]]thread/recent.xml" class="rss_feed">RSS Feed</a></p>
        </div>

        <div class="Box GuestBox">
           <h4>Other Support Options</h4>
            <p>To file a bug or make a feature request <a href="http://bugs.modx.com">visit our issue tracker</a>.</p>
        </div>

        <div class="Box GuestBox">
           <h4>Want to Support MODX?</h4>
            <p>If you build sites for a living with MODX, why not <a href="http://modx.com/community/wall-of-fame/support-modx/">give back</a>?</p>
        </div>

    </div>