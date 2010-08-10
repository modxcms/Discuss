
Dis.grid.UserPosts = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'dis-grid-user-posts'
        ,url: Dis.config.connector_url
        ,baseParams: {
            action: 'mgr/user/post/getlist'
            ,user: config.user
        }
        ,action: 'mgr/user/post/getlist'
        ,fields: ['id','board','parent','title','message','author','createdon','allow_replies','rank','ip','views','sticky','locked','menu']
        ,autoHeight: true
        ,paging: true
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,width: 70
        },{
            header: 'Title'
            ,dataIndex: 'title'
            ,width: 250
        },{
            header: 'Date'
            ,dataIndex: 'createdon'
            ,width: 100
        },{
            header: 'Views'
            ,dataIndex: 'views'
            ,width: 70
        },{
            header: 'IP'
            ,dataIndex: 'ip'
            ,width: 100
        }]
    });
    Dis.grid.UserPosts.superclass.constructor.call(this,config);
};
Ext.extend(Dis.grid.UserPosts,MODx.grid.Grid,{
    getMenu: function() {
        return [{
            text: 'Remove Post'
            ,handler: this.remove.createDelegate(this,[{
                title: 'Remove Post?'
                ,text: 'Are you sure you want to remove this post and all its children?'
            }])
            ,scope: this
        }];
    }
    
    ,updatePost: function(btn,e) {
        var r = {};
        if (this.menu.record) {
            r = this.menu.record;
        }
        
        if (!this.windows.updatePost) {
            this.windows.updatePost = MODx.load({
                xtype: 'dis-window-post-update'
                ,record: r
                ,listeners: {
                    'success': {fn:function() { this.refresh(); },scope:this}
                }
            });
        }
        this.windows.updatePost.setValues(r);
        this.windows.updatePost.show(e.target);
    }
    
    ,removePost: function(btn,e) {
        if (!this.menu.record) return false;
        
        MODx.msg.confirm({
            text: 'Are you sure you want to remove this post and all its replies entirely?'
            ,url: this.config.url
            ,params: {
                action: 'mgr/post/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) { this.refresh(); },scope:this}
            }
        });
        return true;
    }
});
Ext.reg('dis-grid-user-posts',Dis.grid.UserPosts);
