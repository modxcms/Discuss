<?php
/**
 * View all messages for the current user
 *
 * @package discuss
 */
if (!$discuss->isLoggedIn) $modx->sendUnauthorizedPage();
$discuss->setSessionPlace('messages');
$discuss->setPageTitle($modx->lexicon('discuss.messages'));
$placeholders = array();

$limit = !empty($scriptProperties['limit']) ? $scriptProperties['limit'] : $modx->getOption('discuss.threads_per_page',null,20);
$page = !empty($scriptProperties['page']) ? $scriptProperties['page'] : 1;
$page = $page <= 0 ? $page = 1 : $page;
$start = ($page-1) * $limit;

/* get all messages */
$c = $modx->newQuery('disThread');
$c->innerJoin('disPost','FirstPost');
$c->innerJoin('disPost','LastPost');
$c->innerJoin('disUser','LastAuthor');
$c->innerJoin('disThreadUser','Users');
$c->leftJoin('disThreadRead','Reads','Reads.user = '.$discuss->user->get('id').' AND disThread.id = Reads.thread');
$c->where(array(
    'disThread.private' => true,
    'Users.user' => $discuss->user->get('id'),
));
$total = $modx->getCount('disThread',$c);
$c->select($modx->getSelectColumns('disPost','LastPost'));
$c->select(array(
    'disThread.id',
    'disThread.replies',
    'disThread.views',
    'disThread.sticky',
    'disThread.locked',
    'FirstPost.title',
    'post_id' => 'LastPost.id',
    'author' => 'LastPost.author',
    'author_username' => 'LastAuthor.username',
    'viewed' => 'Reads.thread',
));
$c->sortby('LastPost.createdon','DESC');
$c->limit($limit,$start);
$messages = $modx->getCollection('disThread',$c);

$canViewProfiles = $modx->hasPermission('discuss.view_profiles');
$list = array();
$idx = 0;
foreach ($messages as $message) {

    $message->buildIcons();
    $message->buildCssClass('board-post');
    $threadArray = $message->toArray();
    $threadArray['idx'] = $idx;
    $threadArray['createdon'] = strftime($discuss->dateFormat,strtotime($threadArray['createdon']));

    $threadArray['author_link'] = $canViewProfiles ? '<a href="'.$discuss->url.'user/?user='.$threadArray['author'].'">'.$threadArray['author_username'].'</a>' : $threadArray['author_username'];
    $threadArray['views'] = number_format($threadArray['views']);
    $threadArray['replies'] = number_format($threadArray['replies']);
    $threadArray['read'] = 1;

    $threadArray['unread'] = '';
    if (!$threadArray['viewed'] && $discuss->isLoggedIn) {
        $threadArray['unread'] = '<img src="'.$discuss->config['imagesUrl'].'icons/new.png'.'" class="dis-new" alt="" />';
    }

    $list[] = $discuss->getChunk('message/disMessageLi',$threadArray);
    $idx++;
}
$list = implode("\n",$list);
unset($rps,$pa,$recentPosts,$post);

$placeholders['messages'] = $list;
$placeholders['total'] = $total;


/* get board breadcrumb trail */
$trail = array();
$trail[] = array(
    'url' => $discuss->url,
    'text' => $modx->getOption('discuss.forum_title'),
);
$trail[] = array('text' => $modx->lexicon('discuss.messages').' ('.number_format($total).')','active' => true);

$trail = $discuss->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$placeholders['trail'] = $trail;

/* action buttons */
$actionButtons = array();
//$actionButtons[] = array('url' => $discuss->url.'thread/unread?read=1', 'text' => $modx->lexicon('discuss.mark_all_as_read'));
$placeholders['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($actionButtons);

/* build pagination */
$discuss->hooks->load('pagination/build',array(
    'count' => $total,
    'id' => 0,
    'view' => 'messages/index',
    'limit' => $limit,
));

return $placeholders;