[[+usermenu]]
<div class="dis-profile left" style="width: 80%;">

<form class="dis-form" action="[[DiscussUrlMaker? &action=`user/ignoreboards` &params=`{"user":"[[+id]]"}`]]" method="post" style="border: 0;">
    <h2>
        <span class="right" style="padding: 4px;"><input type="checkbox" class="dis-ignore-all" /></span>
        Ignore Boards
    </h2>
    <div class="dis-board-ignores">
        <ul>
            [[+boards]]
        </ul>
    </div>

    <br class="clear" />

    <div class="dis-form-buttons">
        <input type="submit" class="dis-action-btn" name="submit" value="Update" />
    </div>

</form>
</div>
