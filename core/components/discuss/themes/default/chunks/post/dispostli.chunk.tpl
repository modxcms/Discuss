<li class="dis-board-post [[+class]]">
    <div class="right dis-latest" style="width: 25%"><a class="dis-last" href="[[+url]]"></a>
        [[%discuss.last_post]] [[+createdon]]
        <br />[[%discuss.by? &author=`[[+author_link]]`]]
    </div>
    <div class="right" style="width: 10%">[[+replies]]</div>
    <div class="right" style="width: 10%">[[+views]]</div>
    <div class="dis-thread-icons">[[+icons]]</div>
    <div class="dis-thread-body">
        [<a class="dis-board-gray" href="[[DiscussUrlMaker? &action=`board` &params=`{"board":"[[+board]]"}`]]">[[+board_name]]</a>] <a href="[[+url]]">[[+sticky:if=`[[+sticky]]`:eq=`1`:then=`<strong>[[+title]]</strong>`:else=`[[+title]]`]]</a>
        [[+unread:notempty=`<img src="[[++discuss.assets_url]]themes/default/images/icons/new.png" class="dis-new" alt="" />`]]
    </div>
</li>