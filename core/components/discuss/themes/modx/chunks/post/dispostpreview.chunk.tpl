<li class="dis-post" id="dis-post-[[+id]]">
    <div class="dis-post-header">
        <h3 class="dis-post-title" post="0">[[+title]]</h3>
        <div class="dis-post-author" id="dis-post-author-0">
            <div class="dis-post-actions"></div>
            <div class="dis-author">- [[%discuss.post_author_short? &user=`[[+author.username]]` &date=`[[+createdon]]`]]</div>
        </div>
    </div>
    <div class="dis-post-ct" id="dis-thread-ct-[[+id]]">
        <div class="dis-post-body">[[+message]]</div>
        <div class="dis-post-footer">
            <div class="dis-post-reply" id="dis-post-reply-[[+id]]"></div>
            <div class="dis-post-attachments">
            [[+attachments:notempty=`<ul class="dis-attachments">[[+attachments]]</ul>`]]
            </div>
        </div>
    </div>
        <br class="clearfix" />
</li>