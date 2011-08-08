

<form class="dis-form" action="[[~[[*id]]]]search/" method="get">
	<h1 class="Category">[[%discuss.search? &namespace=`discuss` &topic=`web`]]</h1>
	
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
	[[+results:notempty=`<h1 class="Category">Displaying [[+start]]-[[+end]] of [[+total]] Results</h1>`]]

<ol class="dis-board-thread search-results">
[[+results]]
</ol>
</div>

<div class="dis-pagination"><ul>[[+pagination]]</ul></div>

	
</div><!-- Close Content From Wrapper -->

[[+bottom]]



<div id="Panel">
				<hr class="line" />
    <div class="PanelBox">


        <div class="Box GuestBox">
           <h4>Other Support Options</h4>
            <p>To file a bug or make a feature request <a href="http://bugs.modx.com">visit our issue tracker</a>.</p>
        </div>

        <div class="Box GuestBox">
           <h4>Want to Support MODX?</h4>
            <p>If you build sites for a living with MODX, why not <a href="http://modx.com/community/wall-of-fame/support-modx/">give back</a>?</p>
        </div>

    </div>