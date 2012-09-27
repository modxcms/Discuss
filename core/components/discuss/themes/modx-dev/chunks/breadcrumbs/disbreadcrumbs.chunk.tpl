 <div class="m-breadcrumbs f-padinfull l-horizontal_nav h-group">
    <nav class="container l-left">
		<ul>
			[[+items]]
			<li class="end">&nbsp;</li>
		</ul>
	</nav>
	<!-- remove out of breadcrumbs eventually-->
	<div class="l-right m-search">
		<a href="[[~[[*id]]]]thread/recent">[[%discuss.view_recent_posts]]</a> [[%discuss.or_search]]

		<div id="cse-search-form" style="width:300px; display:inline-block; *zoom:1; height:26px;">Loading</div>
		<script src="http://www.google.com/jsapi" type="text/javascript"></script>
		<script type="text/javascript"> 
			google.load('search', '1', {language : 'en', style : google.loader.themes.V2_DEFAULT});
			google.setOnLoadCallback(function() {
			var customSearchOptions = {};  var customSearchControl = new google.search.CustomSearchControl(
			  '016569548712158223163:dxqt7chu3jq', customSearchOptions);
			customSearchControl.setResultSetSize(google.search.Search.FILTERED_CSE_RESULTSET);
			var options = new google.search.DrawOptions();
			options.setAutoComplete(true);
			options.enableSearchboxOnly("http://modx.com/search-temp/", "query");
			customSearchControl.draw('cse-search-form', options);
			}, true);
		</script>
	</div>
[[- 	<form class="l-right m-search" action="[[~[[*id]]]]search" method="get" accept-charset="utf-8">
        <label for="search_form_input" class="hidden">Search</label>
        <input id="search_form_input" placeholder="Search keyphrase..." name="s" value="" title="Start typing and hit ENTER" type="text">
        <input value="Go" type="submit">
    </form> ]]
    <!-- / remove out of breadcrumbs-->
</div>
