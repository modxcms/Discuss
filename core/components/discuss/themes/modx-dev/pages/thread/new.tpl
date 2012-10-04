<!-- new.tpl -->
[[!FormIt?
  &submitVar=`dis-post-reply`
  &hooks=`postHook.DiscussNewThread`
  &validate=`title:required,message:required:allowTags`
]]
<div class="f1-f9 twelve-form">

    <h1>[[%discuss.start_new_thread? &namespace=`discuss` &topic=`post`]]</h1>

    <form action="[[~[[*id]]]]thread/new?board=[[+id]]" method="post" class="dis-form dis-thread-form h-group" id="dis-new-thread-form" enctype="multipart/form-data">
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
        <textarea name="message" id="dis-thread-message">[[!+fi.message]]</textarea>
        <span class="error">[[!+fi.error.message]]</span>

        <div class="l-left">[[+attachment_fields]]</div>
        <div class="dis-form-buttons l-right">
            [[+locked_cb]]
            [[+sticky_cb]]
            <label class="dis-cb"><input type="checkbox" name="notify" value="1" [[!+fi.notify:FormItIsChecked=`1`]] />[[%discuss.notify_of_replies]]</label>
            <input class="cancel" type="button" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]board/?board=[[+id]]';" />
            <input type="submit" class="a-primary-btn" name="dis-post-reply" value="[[%discuss.thread_post_new]]" />
        </div>
    </form>
    [[+discuss.error_panel]]
</div><!-- Close Content From Wrapper -->

[[+bottom]]

[[$post-sidebar?disection=`new-message`]]
<!-- end threads/new.tpl -->
