[[+trail]]

<br />
<form action="[[~[[*id]]]]thread/move?thread=[[+id]]" method="post" class="dis-form" id="dis-remove-thread-form">

    <h2>[[%discuss.thread_remove? &namespace=`discuss` &topic=`post`]]</h2>
    
    <input type="hidden" name="thread" value="[[+id]]" />
    
    <p>[[%discuss.thread_move_message? &thread=`[[+title]]`]]</p>
    
    <span class="error">[[+error]]</span>

    <label for="dis-move-to-board">[[%discuss.board]]:
        <span class="error">[[+error.board]]</span>
    </label>
    <select name="board" id="dis-move-to-board">[[+boards]]</select>

    
    <br class="clear" />
    
    <div class="dis-form-buttons">
    <input type="submit" name="move-thread" class="dis-action-btn" value="[[%discuss.thread_move]]" />
    <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[+url]]';" />
    </div>
</form>