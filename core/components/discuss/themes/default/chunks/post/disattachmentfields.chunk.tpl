<label for="dis-new-thread-attachment">[[%discuss.attachments]]:
    <span class="small dis-new-thread-add-attachment"><a href="[[~[[*id]]]]board/?board=[[+id]]">[[%discuss.attachment_add]]</a>
    <br />([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])</span>
    <span class="error">[[+error.attachments]]</span>
</label>
<input type="file" name="attachment1" id="dis-new-thread-attachment" />
<div id="dis-attachments"></div>
[[+attachments:notempty=`<div class="dis-existing-attachments">
    <ul>[[+attachments]]</ul>
</div>`]]