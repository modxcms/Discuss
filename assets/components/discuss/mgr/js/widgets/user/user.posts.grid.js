
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
        ,fields: ['id','board','parent','title','message','author','createdon','allow_replies','rank','ip','views','sticky','locked']
        ,autoHeight: true
        ,paging: true
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,width: 70
        },{
            header: _('discuss.post_title')
            ,dataIndex: 'title'
            ,width: 250
        },{
            header: _('discuss.post_date')
            ,dataIndex: 'createdon'
            ,width: 100
        },{
            header: _('discuss.post_views')
            ,dataIndex: 'views'
            ,width: 70
        },{
            header: _('discuss.post_ip')
            ,dataIndex: 'ip'
            ,width: 100
        }]
    });
    Dis.grid.UserPosts.superclass.constructor.call(this,config);
};
Ext.extend(Dis.grid.UserPosts,MODx.grid.Grid,{
    getMenu: function() {
        var m = [];
        m.push({
            text: _('discuss.post_modify')
            ,handler: this.updatePost
        });
        m.push('-');
        m.push({
            text: _('discuss.post_remove')
            ,handler: this.removePost
        });

        this.addContextMenuItem(m);
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
            text: _('discuss.post_remove_confirm')
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
