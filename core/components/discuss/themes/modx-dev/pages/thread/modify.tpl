<!-- modify.tpl -->
[[!FormIt?
  &submitVar=`dis-post-reply`
  &hooks=`postHook.DiscussModifyPost`
  &validate=`title:required,message:required:allowTags`
]]
<div class="f1-f9 twelve-form">
    <div>
        <form action="[[~[[*id]]]]thread/modify?post=[[+id]]" method="post" class="dis-form dis-thread-form" id="dis-modify-post-form" enctype="multipart/form-data">
        	<div id="dis-message-preview"></div>
        	<h1>[[%discuss.post_modify? &namespace=`discuss` &topic=`post`]]</h1>
            <input type="hidden" name="board" value="[[!+fi.board]]" />
            <input type="hidden" name="post" value="[[!+fi.post]]" />
            <input type="hidden" name="thread" value="[[!+fi.thread]]" />
            [[+fi.is_root:is=`1`:then=`<label><input type="radio" name="class_key" value="disThreadDiscussion" /> [[%discuss.discussion]]</label>
            	<label><input type="radio" name="class_key" value="disThreadQuestion" /> [[%discuss.question_and_answer]]</label>`]]
            <label for="dis-new-thread-title">[[%discuss.title]]:
                <span class="error">[[!+fi.error.title]]</span>
            </label>
            <input type="text" name="title" id="dis-new-thread-title" value="[[!+fi.title]]" />

            <div class="wysi-buttons">[[+buttons]]</div>
            <label for="dis-thread-message">
                <span class="error">[[!+fi.error.message]]</span>
            </label>
            <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea>

            <div class="h-group">
                <div class="l-left">
                    <label for="dis-attachment">[[%discuss.attachments]]:
                        <span class="small"><a href="javascript:void(0);" class="dis-add-attachment">[[%discuss.attachment_add]]</a>
                        <br />([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span>
                        <span class="error">[[+error.attachments]]</span>
                    </label>
                    <input type="file" name="attachment[[+attachmentCurIdx]]" id="dis-attachment" />
                    <br class="clearfix" />
                    <div id="dis-attachments"></div>
                    [[+attachments:notempty=`<div class="dis-existing-attachments">
                        <ul class="dis-attachments">[[+attachments]]</ul>
                    </div>`]]
                </div>
                <div class="dis-form-buttons l-right">
                    [[+locked_cb]]
                    [[+sticky_cb]]
                    <label class="dis-cb">
                        <input type="checkbox" name="notify" value="1" [[!+fi.notify:FormItIsChecked=`1`]] />[[%discuss.notify_of_replies]]
                    </label>
                    <input class="cancel" type="button" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]board/?board=[[+id]]';" />
                    <input type="submit" class="a-primary-btn" name="dis-post-reply" value="[[%discuss.save_changes]]" />
                </div>
            </div>
        </form>

        <div class="dis-thread-posts">
    		<ul class="dis-list">
    			<li>
                    <h1>[[%discuss.thread_summary]]</h1>
                </li>
    			[[+thread_posts:default=`<p>[[%discuss.thread_no_posts]]</p>`]]
    		</ul>
        </div>
    </div>
    [[+discuss.error_panel]]
</div><!-- Close Content From Wrapper -->

[[+bottom]]

[[$post-sidebar?disection=`new-message`]]

<!-- close thread/modify.tpl -->
