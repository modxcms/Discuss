<p class="dis-breadcrumbs">[[+trail]]</p>

<br />
<form action="[[~[[*id]]]]" method="post" class="dis-form" id="dis-remove-thread-form">

    <h2>Remove Thread</h2>
    
    <input type="hidden" name="thread" value="[[+id]]" />
    
    <p>Are you sure you want to permanently remove the thread "[[+title]]"?</p>
    
    <span class="error">[[+error]]</span>
    
    <br class="clear" />
    
    <div class="dis-form-buttons">
    <input type="submit" name="remove-thread" class="dis-action-btn" value="Remove Thread" />
    <input type="button" class="dis-action-btn" value="Cancel" onclick="location.href='[[~[[++discuss.thread_resource]]]]?thread=[[+id]]';" />
    </div>
</form>