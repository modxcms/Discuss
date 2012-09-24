<script type="text/javascript"><!-- // --><![CDATA[
function bbc_highlight(something, mode) {
    something.style.backgroundImage = "url(" + '[[+buttons_url]]10x10/' + (mode ? "bbc_hoverbg.gif)" : "bbc_bg.gif)");
}
// ]]></script>
<div class="toolbar-btns group">
	<a class="bold" href="javascript:void(0);" onclick="surroundText('[b]', '[/b]'); return false;" class="format_buttons first">Bold</a>
	<a class="italics" href="javascript:void(0);" onclick="surroundText('[i]', '[/i]'); return false;" class="format_buttons">Italics</a>
	<a class="underline" href="javascript:void(0);" onclick="surroundText('[u]', '[/u]'); return false;" class="format_buttons">Underline</a>
	<a class="strikethrough" href="javascript:void(0);" onclick="surroundText('[s]', '[/s]'); return false;" class="format_buttons">Strikethrough</a>
	<a class="insert-image" href="javascript:void(0);" onclick="surroundText('[img]', '[/img]'); return false;" class="format_buttons first">Image</a>
	<a class="url" href="javascript:void(0);" onclick="surroundText('[url]', '[/url]'); return false;" class="format_buttons">URL</a>
	<a class="code" href="javascript:void(0);" onclick="surroundText('[code]', '[/code]'); return false;" class="format_buttons">Insert Code</a>
	[[-<a class="pre" href="javascript:void(0);" onclick="surroundText('[pre]', '[/pre]'); return false;" class="format_buttons">Preformatted Text</a>]]
	<a class="insert-quote" href="javascript:void(0);" onclick="surroundText('[quote]', '[/quote]'); return false;" class="format_buttons">Insert Quote</a>
	[[-<a class="list" href="javascript:void(0);" onclick="surroundText('[list]\n[li]', '[/li]\n[li][/li]\n[/list]'); return false;" class="format_buttons last">Insert List</a>]]
	<a class="ul-list" href="javascript:void(0);" onclick="surroundText('[ul]\n[li]', '[/li]\n[li][/li]\n[/ul]'); return false;" class="format_buttons last">Unordered List</a>
	<a class="ol-list" href="javascript:void(0);" onclick="surroundText('[ol]\n[li]', '[/li]\n[li][/li]\n[/ol]'); return false;" class="format_buttons last">Ordered List</a>

	<div id="dis-message-preview"></div>
	<div class="right preview_toggle">
		<a href="#" class="preview" id="dis-preview-btn">Preview</a>
	</div>
</div>
