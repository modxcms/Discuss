<li class="dis-board-post">
    <div class="right dis-latest" style="width: 25%"><a class="dis-last" href="[[~[[*id]]]]thread/?thread=[[+thread]]#dis-board-post-[[+post_id]]"></a>
        [[%discuss.last_post]] [[+createdon]]
        <br />[[%discuss.by? &author=`<a href="[[~[[*id]]]]user/?user=[[+author]]">[[+author_username]]</a>`]]
    </div>
    <div class="right" style="width: 10%">[[+replies]]</div>
    <div class="right" style="width: 10%">[[+views]]</div>
    <div class="dis-thread-icons">[[+icons]]</div>
    <div class="dis-thread-body">
        [<a class="dis-board-gray" href="[[~[[*id]]]]board/?board=[[+board]]">[[+board_name]]</a>] <a href="[[~[[*id]]]]thread/?thread=[[+thread]]#dis-board-post-[[+post_id]]">[[+sticky:if=`[[+sticky]]`:eq=`1`:then=`<strong>[[+title]]</strong>`:else=`[[+title]]`]]</a>

        [[+unread]]
    </div>
</li>