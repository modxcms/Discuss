<li class="dis-board-post [[+class]]" style="padding-bottom: 10px;">
    <div class="right dis-latest" style="width: 25%"><a class="dis-last" href="[[DiscussUrlMaker? &action=`messages/view` &params=`{"thread":"[[+thread]]"}`]]#dis-board-post-[[+post_id]]"></a>
        [[%discuss.last_post]] [[+createdon]]
        <br />[[%discuss.by? &author=`[[+author_link]]`]]
    </div>
    <div class="right" style="width: 10%">[[+replies]]</div>
    <div class="right" style="width: 10%">[[+views]]</div>
    <div class="dis-thread-icons">[[+icons]]</div>
    <div class="dis-thread-body dis-message-li-body">
        <a href="[[DiscussUrlMaker? &action=`messages/view` &params=`{"thread":"[[+thread]]"}`]]#dis-post-[[+post_id]]">[[+title]]</a>
        [[+unread]]<br />
        <span class="dis-message-author" style="font-size: 10px;">[[%discuss.by? &author=`<a href="[[DiscussUrlMaker? &action=`user` &params=`{"type":"userid","user":"[[+author_first]]"}`]]">[[+author_first_username]]</a>`]]</span>
    </div>
</li>