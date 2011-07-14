<label for="dis-thread-attachment1">[[%discuss.attachments]]:
    <span class="small dis-add-attachment"><a href="javascript:void(0);">[[%discuss.attachment_add]]</a>
    <br />([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span>
    <span class="error">[[!+fi.error.attachments]]</span>
</label>
<input type="file" name="attachment1" id="dis-thread-attachment1" />
<div id="dis-attachments"></div>
[[+attachments:notempty=`<div class="dis-existing-attachments">
    <ul>[[+attachments]]</ul>
</div>`]]