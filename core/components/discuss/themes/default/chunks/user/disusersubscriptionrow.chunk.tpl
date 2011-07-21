<li class="[[+class]]" style="background-image: none;">
    <div class="right" style="padding: 4px 10px; height: 20px; width: 30px;">
        <input type="checkbox" name="remove[]" class="dis-remove-cb" value="[[+id]]" />
    </div>
    <div class="right" style="padding: 4px;">[[+createdon]]</div>

    <a href="[[+url]]">[[+title]]</a>
    <p class="dis-post-li-desc">
        [[%discuss.by? &author=`<a href="[[~[[*id]]]]u/[[+author_username]]">[[+author_username]]</a>`]]
        (<a href="[[~[[*id]]]]board/?board=[[+board]]">[[+board_name]]</a>)
    </p>
</li>