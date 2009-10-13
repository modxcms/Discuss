<p class="dis-breadcrumbs">[[+trail]]</p>

<form class="dis-form" action="[[~[[*id]]]]" method="post">
    <h2>Search</h2>
    
    <label for="dis-search">Search</label>
    <input type="text" name="s" value="[[+search]]" />
    
    <br class="clear" />
    
    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="Search" />
    </div>
</form>

<hr />

<table class="dis-search-results dis-table">
<thead>
<tr>
    <th></th>
    <th>Post</th>
    <th>Excerpt</th>
    <th>Relevancy</th>
    <th>Author</th>
    <th>Posted On</th>
</tr>
</thead>
<tbody>
[[+results]]
</tbody>
</table>
