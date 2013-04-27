<aside class="sidebar twenty12 f10-f12">
    <div class="panel-box">
        [[!+discuss.user.id:is=``:then=`
        <div class="box first">
               <p><a href="[[~[[*id]]]]login" class="primary-cta login dis-action-login">Login to Post</a></p>
               <p>Don't have a MODX.com account? <a href="#">Create one</a></p>
        </div>`]]
        [[!+discuss.user.id:notempty=`
        <div class="box">
            <p>[[+moderators]]</p>
        </div>`]]

<!--This is the chunk-->
        [[- MODX Cloud ad will go here … ]]
        [[!getCache?
            &element=`RandomChunk`
            &cacheKey=`RandomChunkAd`
            &cacheElementKey=`global-rca`
            &cacheExpires=`300`
            &parents=`[[*id]]`
            &chunks=`[[$ad.cloud.ForumsSidebar.RandomAdList]]`
            &imageUrl=`[[+discuss.config.imagesUrl]]`
        ]]

        [[+show_be_nice_box:is=`1`:then=`
        <div class="box">
            <h4>Don't Be That Guy</h4>
            <p>Be nice, respectful and patient. Inflammatory or inappropriate posts will get your post nuked and flood your life with bans and bad karma.</p>
        </div>
        <div class="box">
            <h4>Thank the People that Help</h4>
            <p>Remember, this is an Open Source project and the volunteers here assist out of love for the project and a desire to help others.</p>
        </div>`]]


        <div class="Box GuestBox">
            <div class="a-faux-btn-grp">
                <a class="a-secondary-cta l-inline-btn a-bug" href="http://tracker.modx.com">Found a Bug?</a>
                <a class="a-secondary-cta l-inline-btn a-proposal" href="http://tracker.modx.com/projects/modx-proposals">Have a feature request?</a>
            </div>
            <a class="a-secondary-cta" href="[[~316]]">Buy Emergency Support Now</a>
        </div>

       [[+show_talking:eq=`1`:then=`
        <div class="box">
            <h4>Who’s talking</h4>
            <p>Posted in this thread:<br />[[+participants_usernames]]</p>
            <p>[[+readers]]</p>
        </div>`:else=``]]
    </div>
</aside>



