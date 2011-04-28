<li class="dis-post" id="dis-post-[[+id]]">
    <div class="dis-post-header">
        <h3 class="dis-post-title" post="[[+id]]"><a href="[[~[[*id]]]]thread/?thread=[[+thread]]#dis-post-[[+id]]">[[+title]]</a></h3>
        <div class="dis-post-author" id="dis-post-author-[[+id]]">
            <div class="dis-post-actions">
                [[+action_remove]]
                [[+action_modify]]
                [[+action_quote]]
                [[+action_reply]]
            </div>
            <div class="dis-author">
                [[+author.avatar]]
                <span class="right">
                    [[+createdon]]
                    <br />[[+author.email]]
                </span>
                <span>
                    [[+author.username_link]]
                    [[+author.title:notempty=`<em class="dis-author-title"> - [[+author.title]]</em>`]]
                    <br />
                    [[%discuss.posts]]: <span class="dis-author-post-count">[[+author.posts]]</span>
                </span>
                <br class="clear" />
            </div>
            <div class="dis-author dis-hidden dis-sig-ct-[[+id]]">
                [[+author.signature:notempty=`<div class="dis-signature">[[+author.signature]]</div><div class="clear"></div>`]]
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
                <a href="[[~[[*id]]]]post/track?thread=[[+thread]]&post=[[+id]]">[[+ip]]</a>
            </div>
        </div>
        <br class="clear" />
        [[+children:notempty=`<ol class="dis-board-thread [[+class]]">[[+children]]</ol>`]]
    </div>
</li>