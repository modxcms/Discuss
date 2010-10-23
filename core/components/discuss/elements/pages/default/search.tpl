<p class="dis-breadcrumbs">[[+trail]]</p>

<form class="dis-form" action="[[~[[*id]]]]" method="post">
    <h2>[[%discuss.search? &namespace=`discuss` &topic=`web`]]</h2>
    
    <label for="dis-search">[[%discuss.search]]</label>
    <input type="text" name="s" value="[[+search]]" />
    
    <br class="clear" />
    
    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="[[%discuss.search]]" />
    </div>
</form>

<hr />

<table class="dis-search-results dis-table">
<thead>
<tr>
    <th></th>
    <th>[[%discuss.post]]</th>
    <th>[[%discuss.excerpt]]</th>
    <th>[[%discuss.relevancy]]</th>
    <th>[[%discuss.author]]</th>
    <th>[[%discuss.posted_on]]</th>
</tr>
</thead>
<tbody>
[[+results]]
</tbody>
</table>
