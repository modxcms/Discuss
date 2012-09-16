<aside class="sidebar twenty12 f10-f12">
    [[!+discuss.user.id:is=``:then=`
        <div class="box first">
           <p><a href="[[~[[*id]]]]login" class="primary-cta login">Login to Post</a></p>
           <p>Don't have a MODX.com account? <a href="#">Create one</a></p>
    </div>
    `]]
    <div class="panel-box">
        [[!+discuss.user.id:notempty=`
        <div class="box">
            <h4>Actions</h4>
            <p>[[+actionbuttons]]</p>
            <p>Subscribe:[[+subscribeUrl:notempty=`
                <a class="secondary-cta" href="[[+subscribeUrl]]">By email</a>`]][[+unsubscribeUrl:notempty=`
                <a href="[[+unsubscribeUrl]]">Stop emails</a>
                `]]
            </p>
            <p>[[+moderators]]</p>
        </div>
        `]]
        [[!$post-sidebar-2012?disection=`dis-support-opt`]]
        <div class="box">
            <h4>Information</h4>
            <p>Posted in this thread:<br />[[+participants_usernames]]</p>
            <p>[[+readers]]</p>
        </div>
    </div>
</aside>
