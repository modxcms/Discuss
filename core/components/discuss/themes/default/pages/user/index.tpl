[[+usermenu]]
<div class="dis-profile left" style="width: 80%;">

<form action="[[DiscussUrlMaker? &action=`user` &params=`{"user":"[[+id]]"}`]]" method="post" class="dis-form" id="dis-user-edit-form" style="border: 0;">

<h2>[[+name]]</h2>

<div class="right">
    <img src="[[+avatarUrl]]" alt="[[+username]]" />
    <br /><span class="small">[[+title]]</span>
</div>
<table class="dis-table" style="width: 80%;">
<tbody>
<tr>
    <th>[[%discuss.name? &namespace=`discuss` &topic=`user`]]:</th>
    <td>[[+name_first]] [[+name_last]]</td>
</tr>
<tr>
    <th>[[%discuss.posts]]</th>
    <td>[[+posts]]</td>
</tr>
<tr>
    <th>[[%discuss.groups]]:</th>
    <td>[[+groups]]</td>
</tr>
[[+ip:notempty=`<tr>
    <th>[[%discuss.ip? &namespace=`discuss` &topic=`web`]]:</th>
    <td>[[+ip]]</td>
</tr>`]]
<tr>
    <th>[[%discuss.date_registered]]:</th>
    <td>[[+createdon:strtotime:date=`%b %d, %Y`]]</td>
</tr>
[[+last_active:notempty=`<tr>
    <th>[[%discuss.last_online]]:</th>
    <td>[[+last_active]]</td>
</tr>
<tr>
    <th>[[%discuss.last_reading]]:</th>
    <td><a href="[[+last_post_url]]">[[+lastThread.title]]</a></td>
</tr>`]]
<tr>
    <td colspan="2"><hr /></td>
</tr>
[[+email:notempty=`<tr>
    <th>[[%discuss.email]]:</th>
    <td>[[+email]]</td>
</tr>`]]
<tr>
    <th>[[%discuss.website]]:</th>
    <td>[[+website]]</td>
</tr>
<tr>
    <th>[[%discuss.gender]]:</th>
    <td>[[+gender]]</td>
</tr>
<tr>
    <th>[[%discuss.age]]:</th>
    <td>[[+age]]</td>
</tr>
<tr>
    <th>[[%discuss.location]]:</th>
    <td>[[+location]]</td>
</tr>
</tbody>
</table>
</form>

<br />

<div class="dis-threads">
    <div class="dis-threads-header">
    <h2 style="margin: 0; padding: 0">[[%discuss.recent_posts? &namespace=`discuss` &topic=`web`]]</h2>
    </div>
    <ol class="dis-board-list">
        [[+recent_posts]]
    </ol>
</div>

</div>