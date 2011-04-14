[[+usermenu]]
<div class="dis-profile dis-form left" style="width: 80%; border: 0;">
    <h2>[[%discuss.general_stats? &user=`[[+username]]`]]</h2>
    
    <dl class="dis-datalist">
        <dh>[[%discuss.joined]]:</dh>
        <dt>[[+confirmedon:strtotime:date=`%b %d, %Y %I:%M %p`]]</dt>
        
        <dh>[[%discuss.post_count]]:</dh>
        <dt>[[+posts]]</dt>
        
        <dh>[[%discuss.threads_started]]:</dh>
        <dt>[[+topics]]</dt>

        <dh>[[%discuss.replies]]:</dh>
        <dt>[[+replies]]</dt>
        
        <dh>[[%discuss.last_login]]:</dh>
        <dt>[[+last_login:strtotime:date=`%b %d, %Y %I:%M %p`]]</dt>
        
        <dh>[[%discuss.last_active]]:</dh>
        <dt>[[+last_active:strtotime:date=`%b %d, %Y %I:%M %p`]]</dt>
    </dl>

</div>
