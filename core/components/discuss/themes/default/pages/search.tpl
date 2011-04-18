[[+trail]]
<br />

<form class="dis-form" action="[[~[[*id]]]]search/" method="get">
    <h2>[[%discuss.search? &namespace=`discuss` &topic=`web`]]</h2>
    
    <label for="dis-search">[[%discuss.search]]</label>
    <input type="text" id="dis-search" name="s" value="[[+search]]" />
    
    <br class="clear" />
    
    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="[[%discuss.search]]" />
    </div>
</form>

<hr />

<div class="dis-pagination"><span>[[%discuss.pages? &namespace=`discuss` &topic=`web`]]:</span> <ul>[[+pagination]]</ul></div>

<table class="dis-search-results dis-table">
<thead>
<tr>
    <th style="width: 18%;">[[%discuss.post]]</th>
    <th style="width: 32%;">[[%discuss.excerpt]]</th>
    <th style="width: 5%;">[[%discuss.relevancy]]</th>
    <th style="width: 10%;">[[%discuss.author]]</th>
    <th style="width: 10%;">[[%discuss.posted_on]]</th>
</tr>
</thead>
<tbody>
[[+results]]
</tbody>
</table>

<div class="dis-pagination"><span>[[%discuss.pages]]:</span> <ul>[[+pagination]]</ul></div>