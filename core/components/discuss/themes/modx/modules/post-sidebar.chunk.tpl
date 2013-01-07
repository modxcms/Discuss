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

        [[+show_be_nice_box:is=`1`:then=`
        <div class="box">
            <h4>Don't Be That Guy</h4>
            <p>Be nice, respectful and patient. Inflammatory or inappropriate posts will get your post nuked and flood your life with bans and bad karma.</p>
        </div>
        <div class="box">
            <h4>Thank the People that Help</h4>
            <p>Remember, this is an Open Source project and the volunteers here assist out of love for the project and a desire to help others.</p>
        </div>`]]

        [[- MODX Cloud ad will go here … ]]

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
    [[- Start of Cloud Ad ]]
    <section class="m-cloud-banner">
        <figure>
            <blockquote>
                <p>MODX Cloud makes presenting to clients a breeze.  I can decide which website version to show at any time.</p>
            </blockquote>
             <figcaption class="attribution">
                <div class="wrap left">
                    <span class="avatar"><img src="[[+discuss.config.imagesUrl]]temp/cloud-banners-012013/ben-davis.jpg"></span>
                </div>
                <div class="wrap right">
                    <span class="name">Ben Davis</span>
                    <span class="organization">BD Creative</span>
                </div>
            </figcaption>
        </figure>
        <a class="m-cloud-banner-cta" href="https://modxcloud.com">Try Free 15 Days</a>
        <img src="[[+discuss.config.imagesUrl]]temp/cloud-banners-012013/modx-cloud-logo.png">
    </section>
    [[- End of Cloud Ad ]]

    [[- Start of Alt Cloud Ad ]]
    <section class="m-cloud-banner">
        <p class="m-cloud-banner-msg">Capture and reuse your MODX Projects with in MODX Cloud. </p>
        <a class="m-cloud-banner-cta" href="https://modxcloud.com">Try Free 15 Days</a>
        <img src="[[+discuss.config.imagesUrl]]temp/cloud-banners-012013/modx-cloud-logo.png">
    </section>
    [[- End of Alt Cloud Ad ]]
</aside>



