<li class="[[+class]]" id="dis-post-[[+id]]">
    <div class="dis-post-header">
        <h3 class="dis-post-title" post="[[+id]]"><a href="[[+url]]">[[+title]]<span class="idx">#[[+idx]]</span></a></h3>
        <div class="dis-post-author" id="dis-post-author-[[+id]]">
            <div class="dis-post-actions">
                [[+actions]]
            </div>
            <div class="dis-author">
                <a href="[[~[[*id]]]]user/?user=[[+author.id]]">[[+author.avatar]]</a>
                <span class="right">
                    [[+createdon]]
                    <br />[[+author.email]]
                </span>
                <span>
                    [[+author.username_link]]
                    [[+author.group_badge:notempty=`<img class="group-badge" src="[[+author.group_badge]]" alt="" title="[[+author.group_name]]" />`]]
                    [[+author.title:notempty=`<em class="dis-author-title"> - [[+author.title]]</em>`]]
                    <br />
                    [[%discuss.posts]]: <span class="dis-author-post-count">[[+author.posts]]</span>
                </span>
                <br class="clear" />
            </div>
            <div class="dis-author dis-hidden dis-sig-ct dis-sig-ct-[[+id]]">
                [[+author.signature:notempty=`<div class="dis-signature">[[+author.signature]]<div class="clear"></div></div><div class="clear"></div>`]]
            </div>
        </div>
    </div>
    <div class="dis-post-ct" id="dis-thread-ct-[[+id]]">
        <div class="dis-post-body">[[+content]]</div>
        <div class="dis-post-footer">
            <div class="dis-post-reply" id="dis-post-reply-[[+id]]">[[+action_reply]]</div>
            <div class="dis-post-attachments">
            [[+attachments:notempty=`<ul class="dis-attachments">[[+attachments]]</ul>`]]
            </div>
            <div class="dis-post-ip">
                [[+editedby:is=`0`:then=``:else=`<span class="dis-post-editedon">Edited [[+editedon:ago]] by <a href="[[~[[*id]]]]user?user=[[+editedby]]">[[+editedby.username]]</a></span>`]]
                [[+report_link]]
                <a href="[[~[[*id]]]]post/track?ip=[[+ip]]">[[+ip]]</a>
            </div>
        </div>
        <br class="clear" />
        [[+children:notempty=`<ol class="dis-board-thread [[+class]]">[[+children]]</ol>`]]
    </div>
</li>