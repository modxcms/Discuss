<div class="Depth2 row dis-category h-group dis-category-[[+category]] [[+unread-cls]]">
    <a href="[[+url]]" class="h-group">
        <div class="f1-f7">
            <div class="wrap">
                <strong>[[+name]]</strong>
                <p class="dis-board-description">[[+description]]</p>
            </div>
        </div>
        <div class="f8-f10">[[+last_post_id:neq=``:then=`
            <span class="clickable" data-link="[[+last_post_url]]">[[+last_post_title:default=`&nbsp;`]]</span>
            `:else=`&nbsp;`]]
        </div>
        <div class="f11 l-txtcenter">[[+num_replies]]</div>
        <div class="f12 l-txtcenter">[[+num_topics]]</div>
    </a>
    [[+subforums:notempty=`<div class="h-group f-all"><p class="dis-board-subs [[+unread-cls]]">[[-<strong>Subtopics:</strong>]] [[+subforums]]</p></div>`]]
</div>
