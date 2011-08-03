<li class="dis-post" id="dis-post-[[+id]]">
    <div class="dis-post-header">
        <h3 class="dis-post-title" post="[[+id]]">[[+title]]<span class="idx">#[[+idx]]</span></h3>
        <div class="dis-post-author" id="dis-post-author-[[+id]]">
            <div class="dis-author">
                <span class="right">
                    [[+createdon]]
                </span>
                <span>
                    [[+author.username]]
                </span>
                <br class="clear" />
            </div>
        </div>
    </div>
    <div class="dis-post-ct" id="dis-thread-ct-[[+id]]">
        <div class="dis-post-body">[[+content]]</div>
        <div class="dis-post-footer">
            <div class="dis-post-attachments">
            [[+attachments:notempty=`<ul class="dis-attachments">[[+attachments]]</ul>`]]
            </div>
            <div class="dis-post-ip">
                [[+editedby:is=`0`:then=``:else=`<span class="dis-post-editedon">Edited [[+editedon:ago]] by [[+editedby.username]]</span>`]]
            </div>
        </div>
        <br class="clear" />
        [[+children:notempty=`<ol class="dis-board-thread [[+class]]">[[+children]]</ol>`]]
    </div>
</li>