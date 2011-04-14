<?php
/**
 * Display a thread of posts
 * @package discuss
 */
$discuss->setSessionPlace('thread:'.$_REQUEST['thread']);
$currentResourceUrl = $modx->makeUrl($modx->resource->get('id'));

/* get default properties */
$userId = $modx->user->get('id');
$thread = $modx->getOption('thread',$scriptProperties,false);
if (empty($thread)) $modx->sendErrorPage();

$c = $modx->newQuery('disThread');
$c->innerJoin('disPost','FirstPost');
$c->select($modx->getSelectColumns('disThread','disThread'));
$c->select(array(
    'FirstPost.title',
));
$c->where(array('id' => $thread));
$thread = $modx->getObject('disThread',$c);
if (empty($thread)) $modx->sendErrorPage();

/* mark unread if user clicks mark unread */
if (isset($_REQUEST['unread'])) {
    $props = $thread->toArray();
    $props['recurse'] = true;
    $o = $discuss->loadProcessor('web/post/unread',$props);
    $boardUrl = $currentResourceUrl.'board?board='.$thread->get('board');
    $modx->sendRedirect($boardUrl);
}
if (!empty($_REQUEST['sticky'])) {
    $props = $thread->toArray();
    $props['recurse'] = true;
    $o = $discuss->loadProcessor('web/post/stick',$props);
    $boardUrl = $currentResourceUrl.'board?board='.$thread->get('board');
    $modx->sendRedirect($boardUrl);
}
if (isset($_REQUEST['sticky']) && $_REQUEST['sticky'] == 0) {
    $props = $thread->toArray();
    $props['recurse'] = true;
    $o = $discuss->loadProcessor('web/post/unstick',$props);
    $boardUrl = $currentResourceUrl.'board?board='.$thread->get('board');
    $modx->sendRedirect($boardUrl);
}
if (!empty($_REQUEST['lock'])) {
    $props = $thread->toArray();
    $props['recurse'] = true;
    $o = $discuss->loadProcessor('web/post/lock',$props);
    $boardUrl = $currentResourceUrl.'board?board='.$thread->get('board');
    $modx->sendRedirect($boardUrl);
}
if (isset($_REQUEST['lock']) && $_REQUEST['lock'] == 0) {
    $props = $thread->toArray();
    $props['recurse'] = true;
    $o = $discuss->loadProcessor('web/post/unlock',$props);
    $boardUrl = $currentResourceUrl.'board?board='.$thread->get('board');
    $modx->sendRedirect($boardUrl);
}
if (!empty($_REQUEST['notify'])) {
    $props = $thread->toArray();
    $o = $discuss->loadProcessor('web/post/notify',$props);
    $boardUrl = $currentResourceUrl.'thread?thread='.$thread->get('id');
    $modx->sendRedirect($boardUrl);
}

/* get posts */
$postsOutput = $modx->hooks->load('post/getthread',array(
    'thread' => &$thread,
));
$thread->set('posts',$postsOutput);
unset($postsOutput,$pa,$plist,$userUrl,$profileUrl);

/* load theme options */
//$discuss->config['pa'] = $pa;

/* get board breadcrumb trail */
$c = $modx->newQuery('disBoard');
$c->innerJoin('disBoardClosure','Ancestors');
$c->where(array(
    'Ancestors.descendant' => $thread->get('board'),
));
$c->sortby('Ancestors.depth','DESC');
$ancestors = $modx->getCollection('disBoard',$c);
$trail = array();
$trail[] = array(
    'url' => $currentResourceUrl,
    'text' => $modx->getOption('discuss.forum_title'),
);
foreach ($ancestors as $ancestor) {
    $trail[] = array(
        'url' => $currentResourceUrl.'board?board='.$ancestor->get('id'),
        'text' => $ancestor->get('name'),
    );
}
$trail[] = array('text' => $thread->get('title'), 'active' => true);
$trail = $modx->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$thread->set('trail',$trail);
unset($trail,$url,$c,$ancestors);

/* up the view count for this thread */
$views = $thread->get('views');
$thread->set('views',($views+1));
$thread->save();
unset($views);

$placeholders = $thread->toArray();

/* set activity of thread */
$class = $modx->getOption('cssNormalThread',$scriptProperties,'dis-normal-thread');
$threshold = $modx->getOption('discuss.hot_thread_threshold',null,10);
if ($modx->user->get('id') == $thread->get('author')) {
    $class .= $thread->get('replies') < $threshold ? ' '.$modx->getOption('cssMyNormalThread',$scriptProperties,'dis-my-normal-thread') : ' '.$modx->getOption('cssMyHotThread',$scriptProperties,'dis-my-veryhot-thread');
} else {
    $class .= $thread->get('replies') < $threshold ? '' : ' '.$modx->getOption('cssHotThread',$scriptProperties,'dis-veryhot-thread');
}
$thread->set('class',$class);
unset($class,$threshold);

/* get viewing users */
$placeholders['readers'] = $thread->getViewing();

/* action buttons */
$actionButtons = array();
$currentResourceUrl = $modx->makeUrl($modx->resource->get('id'));

if ($modx->user->isAuthenticated()) {
    $actionButtons[] = array('url' => $currentResourceUrl.'thread?thread=[[+id]]&unread=1', 'text' => $modx->lexicon('discuss.mark_unread'));
    if (!$thread->get('notification')) {
        $actionButtons[] = array('url' => $currentResourceUrl.'thread?thread=[[+id]]&notify=1', 'text' => $modx->lexicon('discuss.notify'));
    }
    /* TODO: Send thread by email - 1.1
     * $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $modx->lexicon('discuss.thread_send'));
     */
    $actionButtons[] = array('url' => $currentResourceUrl.'thread?thread=[[+id]]&print=1', 'text' => $modx->lexicon('discuss.print'));
}
$placeholders['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($actionButtons);

/* thread action buttons */
$actionButtons = array();
if ($modx->user->isAuthenticated()) {
    /** TODO: Move thread - 1.1
     * $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $modx->lexicon('discuss.thread_move'));
     */
    $actionButtons[] = array('url' => $currentResourceUrl.'thread/remove?thread=[[+id]]', 'text' => $modx->lexicon('discuss.thread_remove'));

    if ($thread->get('locked')) {
        $actionButtons[] = array('url' => $currentResourceUrl.'thread?thread=[[+id]]&lock=0', 'text' => $modx->lexicon('discuss.thread_unlock'));
    } else {
        $actionButtons[] = array('url' => $currentResourceUrl.'thread?thread=[[+id]]&lock=1', 'text' => $modx->lexicon('discuss.thread_lock'));
    }
    if ($thread->get('sticky')) {
        $actionButtons[] = array('url' => $currentResourceUrl.'thread?thread=[[+id]]&sticky=0', 'text' => $modx->lexicon('discuss.thread_unstick'));
    } else {
        $actionButtons[] = array('url' => $currentResourceUrl.'thread?thread=[[+id]]&sticky=1', 'text' => $modx->lexicon('discuss.thread_stick'));
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

/* mark as read */
$thread->markAsRead($discuss->user->get('id'));

return $placeholders;