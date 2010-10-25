<div id="board-wrapper">
	<ul class="breadcrumbs">[[+trail]]</ul>
	
	<hr class="clear"/>
	[[+pagination]]
	<ul class="actions right">
		[[+actionbuttons]]
	</ul>

	<hr class="clear"/>

	<h2 class="[[+class]] maintitle">[[%discuss.thread? &namespace=`discuss` &topic=`post`]]: [[+title]] ([[%discuss.views? &views=`[[+views]]`]])</h2>

	<ol class="board-posts">
		[[+posts]]
	</ol>

	[[+pagination]]
	<ul class="actions right">
		[[+actionbuttons]]
	</ul>
	<ul class="actions left">
		[[+threadactionbuttons]]
	</ul>
	<hr class="clear" />
	<div class="readers">
		[[+readers]]
	</div>
	[[+discuss.error_panel]]
	<hr class="clear"/>
</div>