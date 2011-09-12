

<form class="dis-form" action="[[~[[*id]]]]search/" method="get">
	<h1>[[%discuss.search? &namespace=`discuss` &topic=`web`]]</h1>
	
    <label for="dis-search">[[%discuss.search]]:</label><br class="clearfix" />
    <input type="text" id="dis-search" name="s" value="[[+search]]" /><br class="clearfix" />

    <label for="dis-search-board">[[%discuss.board]]:
        <span class="error">[[+error.board]]</span>
    </label><br class="clearfix" />
    <select name="board" id="dis-search-board">[[+boards]]</select><br class="clearfix" />

    <label for="dis-author">[[%discuss.author]]:</label><br class="clearfix" />
    <input type="text" id="dis-author" name="user" value="[[+user]]" />

    <br class="clearfix" />
    
    <div class="dis-form-buttons">
    <input type="submit" class="dis-action-btn" value="[[%discuss.search]]" />
    </div><br class="clearfix" />
</form>

<hr />


<div class="dis-threads">
	

<ul class="dis-list search-results">
<li>[[+results:notempty=`<h1>Displaying [[+start]]-[[+end]] of [[+total]] Results</h1>`]]</li>
[[+results]]
</ul>
</div>

    [[+pagination]]

<br class="clearfix" />

	
</div><!-- Close Content From Wrapper -->

[[+bottom]]



<aside>
				<hr class="line" />
    <div class="PanelBox">


		[[!$post-sidebar?disection=`dis-support-opt`]]


</aside>