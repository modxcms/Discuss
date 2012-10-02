<!-- new.tpl -->
    [[!FormIt?
      &submitVar=`dis-post-reply`
      &hooks=`postHook.DiscussNewThread`
      &validate=`title:required,message:required:allowTags`
    ]]
    <div class="f1-f9 twelve-form">
        <form action="[[~[[*id]]]]thread/new?board=[[+id]]" method="post" class="dis-form dis-thread-form h-group" id="dis-new-thread-form" enctype="multipart/form-data">
            [[-<div class="preview_toggle">
        		<a href="#" class="dis-message-write selected" id="dis-edit-btn">edit</a>
                <a href="#" class="dis-preview" id="dis-preview-btn">view</a>
            </div>]]
        	<h1>[[%discuss.start_new_thread? &namespace=`discuss` &topic=`post`]]</h1>
            <input type="hidden" name="board" value="[[+id]]" />
            
            <div class="h-group">
            	<label>
                    <input type="radio" name="class_key" value="disThreadDiscussion" checked="checked" /> [[%discuss.discussion]]
                </label>
            	<label>
                    <input type="radio" name="class_key" value="disThreadQuestion" /> [[%discuss.question_and_answer]]
                </label>
                <div class="h-group dis-title">
                    <label for="dis-new-thread-title">[[%discuss.title]]:
                        <span class="error">[[!+fi.error.title]]</span>
                    </label>
                    <input type="text" name="title" id="dis-new-thread-title" value="[[!+fi.title]]" />
                </div>
            </div>
            
            <div class="wysi-buttons">[[+buttons]]</div>
            <div id="dis-message-preview"></div>


                <span class="error">[[!+fi.error.message]]</span>
            <textarea name="message" id="dis-thread-message" cols="80" rows="7">[[!+fi.message]]</textarea>

            <div class="h-group">
                <div class="l-left">
                    [[+attachment_fields]]
                </div>
                <div class="dis-form-buttons l-right">
                [[+locked_cb]]
                [[+sticky_cb]]
                    <label class="dis-cb">
                        <input type="checkbox" name="notify" value="1" [[!+fi.notify:FormItIsChecked=`1`]] />[[%discuss.notify_of_replies]]
                    </label>
                    <input class="cancel" type="button" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]board/?board=[[+id]]';" />
                    <input type="submit" name="dis-post-reply" value="[[%discuss.thread_post_new]]" />
                </div>
            </div>
        </form>
        [[+discuss.error_panel]]
    </div>
    <aside class="f10-f12">
        <hr class="line" />
        <div class="PanelBox">
        [[!$post-sidebar?disection=`new-message`]]
    </aside>

</div><!-- Close Content From Wrapper -->

[[+bottom]]

