

<form action="[[~[[*id]]]]messages/remove?thread=[[+id]]" method="post" class="dis-form" id="dis-remove-message-form">

	<ul class="DataList CategoryList CategoryListWithHeadings">
	
	<h1 class="Category">[[%discuss.message_remove? &namespace=`discuss` &topic=`post`]]</h1>

    <input type="hidden" name="thread" value="[[+id]]" />

    <p>[[%discuss.message_remove_confirm? &thread=`[[+title]]`]]</p>

    <span class="error">[[+error]]</span>

    <br class="clearfix" />

    <div class="dis-form-buttons">
    <input type="submit" name="remove-message" class="dis-action-btn" value="[[%discuss.message_remove]]" />
    <input type="button" class="dis-action-btn" value="[[%discuss.cancel]]" onclick="location.href='[[~[[*id]]]]messages/view?message=[[+id]]';" />
    </div>
</form>


			</div><!-- Close Content From Wrapper -->
[[+bottom]]

<div id="Panel">
				<hr class="line" />
    <div class="PanelBox">


		[[!$post-sidebar?disection=`dis-support-opt`]]


    </div>