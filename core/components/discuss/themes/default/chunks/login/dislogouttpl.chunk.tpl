<form class="dis-form dis-login" action="[[DiscussUrlMaker? &action=`login`]]" method="post">
    <h2>[[%discuss.logout? &namespace=`discuss` &topic=`web`]]</h2>

    <div class="dis-form-buttons">
        <input type="button" class="dis-action-btn" value="[[%discuss.logout]]" onclick="location.href='[[DiscussUrlMaker? &action=`logout` &params=`{"service":"logout"}`]]';" />
    </div>
</form>