<label for="dis-attachment">[[%discuss.attachments]]:
    <span class="error">[[+error.attachments]]</span>
</label>
<input type="file" class="dis-attachment-input" name="attachment[[+attachmentCurIdx:default=`1`]]" id="dis-attachment" tabindex="35" />

<div id="dis-attachments"></div>
[[+attachments:notempty=`
    <div class="dis-existing-attachments">
        <ul class="dis-attachments">[[+attachments]]</ul>
    </div>
`]]

<a href="javascript:void(0);" class="dis-add-attachment" tabindex="36">[[%discuss.attachment_add]] <span>([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span></a>
