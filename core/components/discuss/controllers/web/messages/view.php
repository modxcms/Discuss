<?php
/**
 * Display a thread of posts
 * @package discuss
 */
if (!$discuss->isLoggedIn) $modx->sendUnauthorizedPage();

/* get default properties */
$userId = $modx->user->get('id');
$thread = $modx->getOption('message',$scriptProperties,false);
if (empty($thread)) $modx->sendErrorPage();
$discuss->setSessionPlace('message:'.$thread);

$c = $modx->newQuery('disThread');
$c->innerJoin('disPost','FirstPost');
$c->select($modx->getSelectColumns('disThread','disThread'));
$c->select(array(
    'FirstPost.title',
    '(SELECT GROUP_CONCAT(pAuthor.id)
        FROM '.$modx->getTableName('disPost').' AS pPost
        INNER JOIN '.$modx->getTableName('disUser').' AS pAuthor ON pAuthor.id = pPost.author
        WHERE pPost.thread = disThread.id
     ) AS participants',
));
$c->where(array('id' => $thread));
$thread = $modx->getObject('disThread',$c);
if (empty($thread)) $modx->sendErrorPage();
$discuss->setPageTitle($thread->get('title'));

/* get posts */
$posts = $discuss->hooks->load('message/get',array(
    'thread' => &$thread,
));
$thread->set('posts',$posts['results']);
unset($postsOutput,$pa,$plist,$userUrl,$profileUrl);

/* get board breadcrumb trail */
$thread->buildBreadcrumbs(array(array(
    'url' => $discuss->url,
    'text' => $modx->getOption('discuss.forum_title'),
),array(
    'url' => $discuss->url.'messages',
    'text' => $modx->lexicon('discuss.messages'),
)));
unset($trail,$url,$c,$ancestors);

/* up the view count for this thread */
$views = $thread->get('views');
$thread->set('views',($views+1));
$thread->save();
unset($views);

$placeholders = $thread->toArray();
$placeholders['views'] = number_format($placeholders['views']);
$placeholders['replies'] = number_format($placeholders['replies']);

/* set css class of thread */
$thread->buildCssClass();

/* get viewing users */
$placeholders['readers'] = $thread->getViewing('message');

/* get moderator status */
$isModerator = $thread->isModerator($discuss->user->get('id'));

/* action buttons */
$actionButtons = array();
if ($discuss->isLoggedIn) {
    if ($modx->hasPermission('discuss.pm_send')) {
        $actionButtons[] = array('url' => $discuss->url.'messages/reply?message='.$thread->get('id'), 'text' => $modx->lexicon('discuss.reply_to_message'));
    }
    $actionButtons[] = array('url' => $discuss->url.'messages/view?message='.$thread->get('id').'&unread=1', 'text' => $modx->lexicon('discuss.mark_unread'));
    if ($modx->hasPermission('discuss.pm_remove')) {
        $actionButtons[] = array('url' => $discuss->url.'messages/remove?message='.$thread->get('id'), 'text' => $modx->lexicon('discuss.message_remove'));
    }
}
$placeholders['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($actionButtons);

/* output */
$placeholders['discuss.error_panel'] = $discuss->getChunk('Error');
$placeholders['discuss.thread'] = $thread->get('title');

/* set last visited */
if ($discuss->user->get('user') != 0) {
    $discuss->user->set('thread_last_visited',$thread->get('id'));
    $discuss->user->save();
}

/* get pagination */
$discuss->hooks->load('pagination/build',array(
    'count' => $posts['total'],
    'id' => $thread->get('id'),
    'view' => 'thread/',
    'limit' => $posts['limit'],
));

/* mark as read */
$thread->read($discuss->user->get('id'));

$discuss->setPageTitle($thread->get('title'));
return $placeholders;