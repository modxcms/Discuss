<p class="dis-breadcrumbs">[[+trail]]</p>

<div id="dis-new-thread-preview">[[+preview]]</div>
<br />
<form action="[[~[[*id]]]]" method="post" class="dis-form" id="dis-new-thread-form">

    <h2>Start New Thread</h2>
    
    <input type="hidden" name="board" value="[[+board]]" />
    
    <label for="dis-new-thread-title">Title:
        <span class="error">[[+error.title]]</span>
    </label>
    <input type="text" name="title" id="dis-new-thread-title" value="" />
    
    <label for="dis-new-thread-message">Message:
        <span class="error">[[+error.message]]</span>
    </label>
    <textarea name="message" id="dis-new-thread-message" cols="80" rows="7"></textarea>
    <br class="clear" />
    
    <fieldset>
        <legend>Additional Options</legend>
        
        <label class="dis-cb"><input type="checkbox" name="locked" value="1" />Lock this Topic</label>
        <label class="dis-cb"><input type="checkbox" name="sticky" value="1" />Sticky this Topic</label>
        
    </fieldset>

    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="Post New Thread" />
    <input type="button" class="dis-action-btn" id="dis-new-thread-preview-btn" value="Preview" onclick="DISNewThread.preview();" />
    <input type="button" class="dis-action-btn" value="Cancel" onclick="location.href='[[~[[++discuss.board_resource]]]]?board=[[+board]]';" />
    </div>
</form>