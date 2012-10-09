[[+trail]]
<br />

<form class="dis-form" action="[[DiscussUrlMaker? &action=`search`]]" method="get">
    <h2>[[%discuss.search? &namespace=`discuss` &topic=`web`]]</h2>
    
    <label for="dis-search">[[%discuss.search]]:</label>
    <input type="text" id="dis-search" name="s" value="[[+search]]" />

    <label for="dis-search-board">[[%discuss.board]]:
        <span class="error">[[+error.board]]</span>
    </label>
    <select name="board" id="dis-search-board">[[+boards]]</select>

    <label for="dis-author">[[%discuss.author]]:</label>
    <input type="text" id="dis-author" name="user" value="[[+user]]" />

    <label for="dis-date-start">[[%discuss.date_start]]:</label>
    <input type="text" id="dis-date-start" name="date_start" class="date-picker" value="[[+date_start]]" />

    <label for="dis-date-end">[[%discuss.date_end]]:</label>
    <input type="text" id="dis-date-end" name="date_end" class="date-picker" value="[[+date_end]]" />

    <br class="clear" />
    
    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="[[%discuss.search]]" />
    </div>
</form>

<hr />

<div class="dis-pagination"><span>[[%discuss.pages? &namespace=`discuss` &topic=`web`]]:</span> <ul>[[+pagination]]</ul></div>

[[+results:notempty=`<h2>Displaying [[+start]]-[[+end]] of [[+total]] Results</h2>`]]
<table class="dis-search-results dis-table">
<thead>
<tr>
    <th style="width: 18%;">[[%discuss.post]]</th>
    <th style="width: 32%;">[[%discuss.excerpt]]</th>
    <th style="width: 10%;">[[%discuss.author]]</th>
    <th style="width: 10%;">[[%discuss.posted_on]]</th>
</tr>
</thead>
<tbody>
[[+results]]
</tbody>
</table>

<div class="dis-pagination"><span>[[%discuss.pages]]:</span> <ul>[[+pagination]]</ul></div>