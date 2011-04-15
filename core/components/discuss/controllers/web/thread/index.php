<?php
/**
 * Display a thread of posts
 * @package discuss
 */
$discuss->setSessionPlace('thread:'.$scriptProperties['thread']);

/* get default properties */
$userId = $modx->user->get('id');
$thread = $modx->getOption('thread',$scriptProperties,false);
if (empty($thread)) $modx->sendErrorPage();

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

/* mark unread if user clicks mark unread */
if (isset($scriptProperties['unread'])) {
    if ($thread->unread($discuss->user->get('id'))) {
        $modx->sendRedirect($discuss->url.'board?board='.$thread->get('board'));
    }
}
if (!empty($scriptProperties['sticky'])) {
    if ($thread->stick()) {
        $modx->sendRedirect($discuss->url.'board?board='.$thread->get('board'));
    }
}
if (isset($scriptProperties['sticky']) && $scriptProperties['sticky'] == 0) {
    if ($thread->unstick()) {
        $modx->sendRedirect($discuss->url.'board?board='.$thread->get('board'));
    }
}
if (!empty($scriptProperties['lock'])) {
    if ($thread->lock()) {
        $modx->sendRedirect($discuss->url.'board?board='.$thread->get('board'));
    }
}
if (isset($scriptProperties['lock']) && $scriptProperties['lock'] == 0) {
    if ($thread->unlock()) {
        $modx->sendRedirect($discuss->url.'board?board='.$thread->get('board'));
    }
}
if (!empty($scriptProperties['notify'])) {
    if ($thread->addNotify($discuss->user->get('id'))) {
        $modx->sendRedirect($discuss->url.'thread?thread='.$thread->get('id'));
    }
}

/* get posts */
$posts = $discuss->hooks->load('post/getThread',array(
    'thread' => &$thread,
));
$thread->set('posts',$posts['results']);
unset($postsOutput,$pa,$plist,$userUrl,$profileUrl);

/* load theme options */
//$discuss->config['pa'] = $pa;

/* get board breadcrumb trail */
$thread->buildBreadcrumbs();
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
$placeholders['readers'] = $thread->getViewing();

/* action buttons */
$actionButtons = array();
if ($discuss->isLoggedIn) {
    $actionButtons[] = array('url' => $discuss->url.'thread?thread='.$thread->get('id').'&unread=1', 'text' => $modx->lexicon('discuss.mark_unread'));
    if (!$thread->hasNotification($discuss->user->get('id'))) {
        $actionButtons[] = array('url' => $discuss->url.'thread?thread='.$thread->get('id').'&notify=1', 'text' => $modx->lexicon('discuss.notify'));
    }
    /* TODO: Send thread by email - 1.1
     * $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $modx->lexicon('discuss.thread_send'));
     */
    //$actionButtons[] = array('url' => $discuss->url.'thread?thread='.$thread->get('id').'&print=1', 'text' => $modx->lexicon('discuss.print'));
}
$placeholders['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($actionButtons);

/* thread action buttons */
$actionButtons = array();
if ($discuss->isLoggedIn) {
    /** TODO: Move thread - 1.1
     * $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $modx->lexicon('discuss.thread_move'));
     */
    $actionButtons[] = array('url' => $discuss->url.'thread/remove?thread='.$thread->get('id'), 'text' => $modx->lexicon('discuss.thread_remove'));

    if ($thread->get('locked')) {
        $actionButtons[] = array('url' => $discuss->url.'thread?thread='.$thread->get('id').'&lock=0', 'text' => $modx->lexicon('discuss.thread_unlock'));
    } else {
        $actionButtons[] = array('url' => $discuss->url.'thread?thread='.$thread->get('id').'&lock=1', 'text' => $modx->lexicon('discuss.thread_lock'));
    }
    if ($thread->get('sticky')) {
        $actionButtons[] = array('url' => $discuss->url.'thread?thread='.$thread->get('id').'&sticky=0', 'text' => $modx->lexicon('discuss.thread_unstick'));
    } else {
        $actionButtons[] = array('url' => $discuss->url.'thread?thread='.$thread->get('id').'&sticky=1', 'text' => $modx->lexicon('discuss.thread_stick'));
    }
    /**
     * TODO: Merge thread - 1.1
     * $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $modx->lexicon('discuss.thread_merge'));
     */
}
$placeholders['threadactionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
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

return $placeholders;