[[+top]]
<div class="right">
    <form action="[[~[[*id]]]]search" method="GET">
        <input type="hidden" name="board" value="[[+id]]" />
        <input type="text" name="s" value="" class="dis-form-field-solo" style="width: 200px; margin-right: 5px;" placeholder="[[%discuss.search_this_board]]" />

        <input type="submit" class="dis-action-btn-solo"  value="[[%discuss.search]]" />
    </form>
</div>
[[+trail]]

[[+aboveBoards]]
<ol class="dis-board-list" style="[[+boards_toggle]]">
[[+boards]]
</ol>

[[+belowBoards]]

<br class="clear" />

[[+actionbuttons]]

<div class="dis-pagination"><span>[[%discuss.pages? &namespace=`discuss` &topic=`web`]]:</span> <ul>[[+pagination]]</ul></div>

<br class="clear" />

<div class="dis-threads">
<div class="dis-threads-header">
    <div class="dis-threads-ct">
        <div class="right" style="width: 25%">[[%discuss.last_post]]</div>
        <div class="right" style="width: 10%">[[%discuss.replies]]</div>
        <div class="right" style="width: 10%">[[%discuss.views]]</div>
        <div class="dis-threads-body right" style="width: 55%;">[[%discuss.message]]</div>
    </div>
    <br class="clear" />
</div>
<ol>
[[+posts]]
</ol>
</div>

<br class="clear" />

[[+actionbuttons]]

<div class="dis-pagination"><span>[[%discuss.pages]]:</span> <ul>[[+pagination]]</ul></div>

[[+belowThreads]]

<p class="dis-thread-viewing clear">[[+readers]]</p>
<p class="dis-moderators">[[+moderators]]</p>
<p class="dis-breadcrumbs clear">[[+trail]]</p>

[[+bottom]]