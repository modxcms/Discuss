<li class="[[+class]]" style="background-image: none;">
    <div class="right" style="padding: 4px 10px; height: 20px; width: 30px;">
        <input type="checkbox" name="remove[]" class="dis-remove-cb" value="[[+id]]" />
    </div>
    <div class="right" style="padding: 4px;">[[+createdon:strtotime:date=`[[++discuss.date_format]]`]]</div>

    <a href="[[~[[++discuss.thread_resource]]]]?thread=[[+thread]]#dis-board-post-[[+id]]">[[+title]]</a>
    <p class="dis-post-li-desc">
        by <a href="[[~[[++discuss.user_resource]]]]?user=[[+author]]">[[+author_username]]</a> 
        (<a href="[[~[[++discuss.board_resource]]]]?board=[[+board]]">[[+board_name]]</a>)
    </p>
</li>