[[+top]]

[[+aboveThread]]


<h1 class="Category [[+locked:is=`1`:then=`locked`:else=`unlocked`]]" post="[[+id]]"><a href="[[+url]]">[[+title]]<span class="idx">#[[+idx]]</span></a></h1>


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

            <textarea name="message" id="dis-thread-message">[[+message]]</textarea>

            [[+attachment_fields]]
            <br class="clearfix" />

            [[+locked_cb]]
            [[+sticky_cb]]
            

            <div class="dis-form-buttons" style="">
               <input type="submit" name="dis-post-reply" value="Reply" /> <label class="dis-cb"><input type="checkbox" name="notify" value="1" />Subscribe to replies via email</label>
            </div>
            <br class="clearfix" />
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


		[[$actions-sidebar]]


    </div>