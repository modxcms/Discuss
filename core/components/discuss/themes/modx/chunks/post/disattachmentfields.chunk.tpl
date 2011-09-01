<label for="dis-thread-attachment1">
    	<a href="javascript:void(0);">[[%discuss.attachment_add]]</a> ([[%discuss.attachments_max? &max=`[[+max_attachments]]`]])
    <span class="error">[[!+fi.error.attachments]]</span>
</label>
	
	<br class="clearfix" />

<input type="file" name="attachment1" id="dis-thread-attachment1" />
	<div id="dis-attachments"></div>
	[[+attachments:notempty=`<div class="dis-existing-attachments">
    <ul>[[+attachments]]</ul>
</div>`]]