
<form class="m-fullw-form m-styled-form h-group" action="[[~[[*id]]]]search/" method="get">
	<h1>[[%discuss.search? &namespace=`discuss` &topic=`web`]]</h1>
    <div class="m-panel f1-f8">
        <div class="f1-f5 f-pad">
            <label class="search" for="dis-search">[[%discuss.search]]:</label>
            <input class="search" type="text" id="dis-search" name="s" value="[[+search]]" />
        </div>
        <div class="f1-f8 f-pad">
            <label for="dis-search-board">[[%discuss.board]]:
                <span class="error">[[+error.board]]</span>
            </label>
        </div>
        <div class="f1-f8 f-pad">
            <select name="board" id="dis-search-board">[[+boards]]</select>
        </div>
        <div class="f1-f8 f-pad">
            <label for="dis-author">[[%discuss.author]]:</label>
            <input type="text" id="dis-author" name="user" value="[[+user]]" />
        </div>

        <div>
            <div class="f1-f4 f-pad">
                <label for="dis-date-start">[[%discuss.date_start]]:</label>
                <input type="text" id="dis-date-start" class="m-datepicker" name="date_start" value="[[+date_start]]"/>
            </div>

            <div class="f5-f8 f-pad">
                <label for="dis-date-end">[[%discuss.date_end]]:</label><br class="clearfix" />
                <input type="text" id="dis-date-end" class="m-datepicker" name="date_end" value="[[+date_end]]"/>
            </div>
        </div>

        <div class="f1-f8 f-pad">
            <input type="submit" class="dis-action-btn" value="[[%discuss.search]]" />
        </div>
    </div>
</form>


[[+pagination]]
<div class="dis-threads">
    <ul class="dis-list search-results">
        <li>[[+results:notempty=`<h1>Displaying [[+start]]-[[+end]] of [[+total]] Results</h1>`]]</li>
        [[+results]]
    </ul>
</div>
[[+pagination]]

[[+bottom]]
