[[!FormIt?
  &submitVar=`dis-post-reply`
  &hooks=`postHook.[[+hook]]`
  &validate=`title:required,message:required:allowTags`
]]

<h1>[[%discuss.post_[[+action]]? &namespace=`discuss` &topic=`post`]]</h1>
[[!+fi.error.test]]
<form action="[[~[[*id]]]]thread/[[+action]]?[[+actionvar]]=[[+id]]" method="post" class="dis-form dis-thread-form" id="dis-post-form" enctype="multipart/form-data">
    <input type="hidden" name="board"  value="[[!+fi.board]]" />
    <input type="hidden" name="thread" value="[[!+fi.thread]]" />
    <input type="hidden" name="post"   value="[[!+fi.post]]" />

    [[!+fi.is_root:is=`1`:then=`
        <section class="group">
            <div class="m-dis-thread-type-wrap">
                <ul class="m-dis-thread-type horiz-list">
                    <li class="m-dis-discussion" data-target=".m-dis-discussion-info">
                        <label>
                            <input type="radio" name="class_key" value="disThreadDiscussion" [[!+fi.class_key:eq=`disThreadDiscussion`:then=`checked="checked"`:else=``]] />
                            [[%discuss.discussion]]
                        </label>
                    </li>
                    <li class="m-dis-question" data-target=".m-dis-question-info">
                        <label>
                            <input type="radio" name="class_key" value="disThreadQuestion" [[!+fi.class_key:eq=`disThreadQuestion`:then=`checked="checked"`:else=``]] />
                            [[%discuss.question]]
                        </label>
                    </li>
                </ul>
            </div>
            <div class="dis-thread-info">
                <ul>
                    <li class="m-dis-choose-info"><p>&laquo; [[%discuss.new_post_type_instructions]] </p></li>
                    <li class="m-dis-discussion-info" style="display: none;"><p>[[%discuss.discussion_instructions]] [[%discuss.discussion_links]]</p></li>
                    <li class="m-dis-question-info" style="display: none;"><p>[[%discuss.question_instructions]]<p><p>[[%discuss.question_links]]</p></li>
                </ul>
            </div>
            <br class="clearfix" />
        </section>

        <label for="dis-new-thread-title">[[%discuss.title]]:
            <span class="error">[[!+fi.error.title]]</span>
        </label>
        <input type="text" name="title" id="dis-new-thread-title" class="dis-new-thread-title" value="[[!+fi.title]]" />
    `:else=`
        <input type="hidden" name="title" value="[[!+fi.title]]" />
    `]]

    <div class="wysi-buttons">[[+buttons]]</div>
    <textarea name="message" id="dis-thread-message">[[!+fi.message]]</textarea>
    <span class="error">[[!+fi.error.message]]</span>

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
            [[+attachments:notempty=`
                <div class="dis-existing-attachments">
                    <ul class="dis-attachments">[[+attachments]]</ul>
                </div>
            `]]
        </div>

        <div class="dis-form-buttons l-right">
            [[+locked_cb]]
            [[+sticky_cb]]
            <label class="dis-cb">
                <input type="checkbox" name="notify" value="1" checked="checked" />[[%discuss.subscribe_by_email]]
            </label>
            <input class="cancel" type="button" value="[[%discuss.cancel]]" onclick="location.href='[[+url]]';" />
            <input type="submit" name="dis-post-reply" value="[[%discuss.post_[[+action]]]]" />
        </div>
    </div>
    [[+discuss.error_panel]]
</form>
