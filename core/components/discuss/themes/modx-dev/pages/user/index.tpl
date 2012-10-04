[[+top]]

    <div class="dis-profile f1-f9">
        <form action="[[~[[*id]]]]user/?user=[[+id]]" method="post" id="dis-user-edit-form">
        	<h1>[[+name]]</h1>
            <ul class="profile">
                <li class="f1-f5">
                    <div class="f1-f2">[[%discuss.name? &namespace=`discuss` &topic=`user`]]:</div>
                    <div class="f3-f5"><strong>[[+name_first]] [[+name_last]]</strong></div>
                 </li>
                <li class="f1-f5">
                    <div class="f1-f2">[[%discuss.posts]]:</div>
                    <div class="f3-f5"><strong>[[+posts]]</strong></div>
                </li>
                [[+ip:notempty=`
                <li class="f1-f5">
                    <div class="f1-f2">[[%discuss.ip? &namespace=`discuss` &topic=`web`]]:</div>
                    <div class="f3-f5"><strong>[[+ip]]</strong></div>
                </li>`]]
                <li class="f1-f5">
                    <div class="f1-f2">[[%discuss.date_registered]]: </div>
                    <div class="f3-f5"><strong>[[+createdon:strtotime:date=`%b %d, %Y`]]</strong></div>
                </li>
                [[+last_active:notempty=`
                <li class="f1-f5">
                    <div class="f1-f2">[[%discuss.last_online]]:</div>
                    <div class="f3-f5"><strong>[[+last_active]]</strong></div>
                </li>
                <li class="f1-f5">
                    <div class="f1-f2">[[%discuss.last_reading]]:</div>
                    <div class="f3-f5"><strong><a href="[[+last_post_url]]">[[+lastThread.title]]</a></strong></div>
                </li>`]]
                [[+email:notempty=`
                <li class="f1-f5">
                    <div class="f1-f2">[[%discuss.email]]:</div>
                    <div class="f3-f5"><strong><a href="mailto:[[+email]]">[[+email]]</a></strong></div>
                </li>`]]
                <li class="f1-f5">
                    <div class="f1-f2">[[%discuss.website]]:</div>
                    <div class="f3-f5"><strong>[[+website]]</strong></div>
                </li>
                <li class="f1-f5">
                    <div class="f1-f2">[[%discuss.gender]]:</div>
                    <div class="f3-f5"><strong>[[+gender]]</strong></div>
                </li>
                <li class="f1-f5">
                    <div class="f1-f2">[[%discuss.age]]:</div>
                    <div class="f3-f5"><strong>[[+age]]</strong></div>
                </li>
                <li class="f1-f5">
                    <div class="f1-f2">[[%discuss.location]]:</div>
                    <div class="f3-f5"><strong>[[+location]]</strong></div>
                </li>
            </ul>
        </form>
    </div>

    [[+sidebar]]
</div><!-- Close Content From Wrapper -->
[[+bottom]]


