[[+top]]

[[+aboveThread]]

	<h1 class="Category [[+locked:is=`1`:then=`locked`:else=`unlocked`]]">[[+title]]</h1>

	<div>
		<ol class="dis-board-thread">
			[[+posts]]
			<li>[[+pagination]]</li>
		</ol>

            <div class="preview_toggle">
                <a href="#" class="dis-message-write selected" id="dis-message-write-btn">write</a>
                <a href="#" class="dis-preview" id="dis-post-reply-preview">preview</a>
            	<div id="dis-reply-post-preview"></div>
            </div>

		<form action="[[~[[*id]]]]thread/reply" method="post" class="dis-form [[+locked:notempty=`locked`]]" id="dis-quick-reply-form" enctype="multipart/form-data">
            
            <input type="hidden" id="dis-quick-reply-board" name="board" value="[[+board]]" />
            <input type="hidden" id="dis-quick-reply-thread" name="thread" value="[[+id]]" />
            <input type="hidden" id="dis-quick-reply-post" name="post" value="[[+lastPost.id]]" />

            <input type="hidden" name="title" id="dis-quick-reply-title" value="Re: [[+title]]" />

            <div class="wysi-buttons">[[+reply_buttons]]</div>

            <textarea name="message" id="dis-quick-reply-message" rows="7" cols="80">[[+message]]</textarea>

            [[+attachment_fields]]
            <br class="clearfix" />

            [[+locked_cb]]
            [[+sticky_cb]]
            

            <br class="clearfix" />
            <div class="dis-form-buttons">
               <input type="submit" name="dis-post-reply" value="Reply" /> <label class="dis-cb"><input type="checkbox" name="notify" value="1" />Subscribe to replies via email</label>
            </div>

        </form>
        

	<div class="dis-thread-actions">[[+threadactionbuttons]]</div>

	<br class="clearfix" />

	</div>



	
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

            <p>[[+readers]] [[+views]] total views.</p>
            <p>Subscribe: <a href="[[~[[*id]]]]thread/recent.xml">RSS</a> or [[+subscribed:is=`1`:then=`<a href="[[+unsubscribeUrl]]">stop emails</a>`:else=`<a href="[[+subscribeUrl]]">email</a>`]]</p>
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