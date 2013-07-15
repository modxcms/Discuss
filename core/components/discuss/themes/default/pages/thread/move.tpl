<!-- move.tpl -->
<form action="[[DiscussUrlMaker? &action=`thread/move` &params=`{"thread":"[[+id]]"}`]]" method="post" class="dis-form" id="dis-remove-thread-form">
	<h1>[[%discuss.thread_move? &namespace=`discuss` &topic=`post`]]</h1>
    <p>[[%discuss.thread_move_message? &thread=`[[+title]]`]]</p>
    
    <input type="hidden" name="thread" value="[[+id]]" />
    <span class="error">[[+error]]</span>

    <label for="dis-move-to-board">[[%discuss.board]]:
        <span class="error">[[+error.board]]</span>
    </label>
    <select name="board" id="dis-move-to-board">[[+boards]]</select>
    
    <div class="dis-form-buttons">
        <input type="submit" class="Button" value="[[%discuss.thread_move]]" name="move-thread" />
        <input type="button" class="Button" value="[[%discuss.cancel]]" onclick="location.href='[[+url]]';" />
    </div>
</form>

[[+bottom]]

[[+sidebar]]
