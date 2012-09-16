 <div class="m-breadcrumbs f-padinfull l-horizontal_nav h-group">
    <nav class="container l-left">
		<ul>
			[[+items]]
			<li class="end">&nbsp;</li>
		</ul>
	</nav>
	<!-- remove out of breadcrumbs eventually-->
	<form class="l-right" action="[[~[[*id]]]]search" method="get" accept-charset="utf-8">
        <label for="search_form_input" class="hidden">Search</label>
        <input id="search_form_input" placeholder="Search keyphrase..." name="s" value="" title="Start typing and hit ENTER" type="text">
        <input value="Go" type="submit">
    </form>
    <!-- / remove out of breadcrumbs-->
</div>
