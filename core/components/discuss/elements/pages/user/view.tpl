[[+usermenu]]
<div class="dis-profile left" style="width: 80%;">
<table class="dis-table">
<thead>
<tr>
    <td colspan="2"><h2>[[+username]]</h2></td>
</tr>
</thead>
<tbody>
<tr>
    <th>Name</th>
    <td>[[+name_first]] [[+name_last]]</td>
</tr>
<tr>
    <th>Posts</th>
    <td>[[+posts]]</td>
</tr>
<tr>
    <th>IP:</th>
    <td>[[+ip]]</td>
</tr>
<tr>
    <th>Date Registered:</th>
    <td>[[+createdon:strtotime:date=`%b %d, %Y`]]</td>
</tr>
<tr>
    <th>Last Online:</th>
    <td>[[+last_active:strtotime:date=`[[++discuss.date_format]]`]]</td>
</tr>
<tr>
    <th>Last Reading:</th>
    <td><a href="[[~[[++discuss.thread_resource]]]]?thread=[[+lastThread.id]]">[[+lastThread.title]]</a></td>
</tr>
<tr>
    <td colspan="2"><hr /></td>
</tr>
<tr>
    <th>Email:</th>
    <td>[[+email]]</td>
</tr>
<tr>
    <th>Website:</th>
    <td>[[+website]]</td>
</tr>
<tr>
    <th>Gender</th>
    <td>[[+gender]]</td>
</tr>
<tr>
    <th>Age:</th>
    <td>[[+age]]</td>
</tr>
<tr>
    <th>Location:</th>
    <td>[[+location]]</td>
</tr>
</tbody>
</table>

<br />

<ol class="dis-board-list" style="border: 0;">
    <li class="dis-category-li"><h2>[[%discuss.recent_posts]]</h2></li>
    [[+recentPosts]]
</ol>

</div>