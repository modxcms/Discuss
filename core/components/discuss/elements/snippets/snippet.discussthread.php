<?php
/**
 *
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));
$discuss->setSessionPlace('thread:'.$_REQUEST['thread']);


/* get default properties */
$userId = $modx->user->get('id');
$thread = $modx->getOption('thread',$_REQUEST,$modx->getOption('thread',$scriptProperties,false));

if (empty($thread)) $modx->sendErrorPage();

/* get thread root */
$otherSelect = '';
$c = $modx->newQuery('disPost');
if (!empty($userId)) {
    $c->leftJoin('disUserNotification','Notifications','`disPost`.`id` = `Notifications`.`post` AND `Notifications`.`user` = '.$userId);
    $otherSelect = ', `Notifications`.`user` AS `notification`';
}
$c->select('
    `disPost`.*,
    (SELECT COUNT(*) FROM '.$modx->getTableName('disPostClosure').'
     WHERE
        `ancestor` = `disPost`.`id`
    AND `descendant` != `disPost`.`id`) AS `replies`'.$otherSelect);
$c->where(array(
    'id' => $thread,
));
$thread = $modx->getObject('disPost',$c);
if ($thread === null) $modx->sendErrorPage();
unset($c);

/* mark unread if user clicks mark unread */
if (isset($_REQUEST['unread'])) {
    $props = $thread->toArray();
    $props['recurse'] = true;
    $o = $discuss->loadProcessor('web/post/unread',$props);
    $boardUrl = $modx->makeUrl($modx->getOption('discuss.board_resource'),'','?board='.$thread->get('board'));
    $modx->sendRedirect($boardUrl);
}
if (!empty($_REQUEST['sticky'])) {
    $props = $thread->toArray();
    $props['recurse'] = true;
    $o = $discuss->loadProcessor('web/post/stick',$props);
    $boardUrl = $modx->makeUrl($modx->getOption('discuss.board_resource'),'','?board='.$thread->get('board'));
    $modx->sendRedirect($boardUrl);
}
if (isset($_REQUEST['sticky']) && $_REQUEST['sticky'] == 0) {
    $props = $thread->toArray();
    $props['recurse'] = true;
    $o = $discuss->loadProcessor('web/post/unstick',$props);
    $boardUrl = $modx->makeUrl($modx->getOption('discuss.board_resource'),'','?board='.$thread->get('board'));
    $modx->sendRedirect($boardUrl);
}
if (!empty($_REQUEST['lock'])) {
    $props = $thread->toArray();
    $props['recurse'] = true;
    $o = $discuss->loadProcessor('web/post/lock',$props);
    $boardUrl = $modx->makeUrl($modx->getOption('discuss.board_resource'),'','?board='.$thread->get('board'));
    $modx->sendRedirect($boardUrl);
}
if (isset($_REQUEST['lock']) && $_REQUEST['lock'] == 0) {
    $props = $thread->toArray();
    $props['recurse'] = true;
    $o = $discuss->loadProcessor('web/post/unlock',$props);
    $boardUrl = $modx->makeUrl($modx->getOption('discuss.board_resource'),'','?board='.$thread->get('board'));
    $modx->sendRedirect($boardUrl);
}
if (!empty($_REQUEST['notify'])) {
    $props = $thread->toArray();
    $o = $discuss->loadProcessor('web/post/notify',$props);
    $boardUrl = $modx->makeUrl($modx->getOption('discuss.thread_resource'),'','?thread='.$thread->get('id'));
    $modx->sendRedirect($boardUrl);
}

/* mark posts in thread read */
$children = $thread->getDescendants();
foreach ($children as $child) {
    $child->markAsRead();
}


$postsOutput = $modx->hooks->load('post/getthread',array(
    'thread' => &$thread,
));
$thread->set('posts',$postsOutput);
unset($postsOutput,$pa,$plist,$userUrl,$profileUrl);

/* register javascript */
$modx->regClientStartupScript($discuss->config['jsUrl'].'web/dis.thread.js');
$modx->regClientStartupScript('<script type="text/javascript">
$(function() {
    DIS.config.postCount = "'.count($pa).'";
});</script>');

/* get board breadcrumb trail */
$c = $modx->newQuery('disBoard');
$c->innerJoin('disBoardClosure','Ancestors');
$c->where(array(
    'Ancestors.descendant' => $thread->get('board'),
));
$c->sortby('Ancestors.depth','DESC');
$ancestors = $modx->getCollection('disBoard',$c);
$trail = '<a href="'.$modx->makeUrl($modx->getOption('discuss.board_list_resource')).'">'
    .'[[++discuss.forum_title]]'
    .'</a> / ';
foreach ($ancestors as $ancestor) {
    $url = $modx->makeUrl($modx->getOption('discuss.board_resource'),'','?board='.$ancestor->get('id'));
    $trail .= '<a href="'.$url.'">'.$ancestor->get('name').'</a>';
    $trail .= ' / ';
}
$trail .= $thread->get('title');
$thread->set('trail',$trail);
unset($trail,$url,$c,$ancestors);

/* up the view count for this thread */
$views = $thread->get('views');
$thread->set('views',($views+1));
$thread->save();
unset($views);

$properties = $thread->toArray();

/* set activity of thread */
$class = $modx->getOption('cssNormalThreadCls',$scriptProperties,'dis-normal-thread');
$threshold = $modx->getOption('discuss.hot_thread_threshold',null,10);
if ($modx->user->get('id') == $thread->get('author')) {
    $class .= $thread->get('replies') < $threshold ? ' '.$modx->getOption('cssMyNormalThreadCls',$scriptProperties,'dis-my-normal-thread') : ' '.$modx->getOption('cssMyHotThreadCls',$scriptProperties,'dis-my-veryhot-thread');
} else {
    $class .= $thread->get('replies') < $threshold ? '' : ' '.$modx->getOption('cssHotThreadCls',$scriptProperties,'dis-veryhot-thread');
}
$thread->set('class',$class);
unset($class,$threshold);

/* get viewing users */
$properties['readers'] = $thread->getViewing();

/* action buttons */
$actionButtons = array();
if ($modx->user->isAuthenticated()) {
    $actionButtons[] = array('url' => '[[~[[++discuss.thread_resource]]]]?thread=[[+id]]&unread=1', 'text' => $modx->lexicon('discuss.mark_unread'));
    if (!$thread->get('notification')) {
        $actionButtons[] = array('url' => '[[~[[++discuss.thread_resource]]]]?thread=[[+id]]&notify=1', 'text' => $modx->lexicon('discuss.notify'));
    }
    $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $modx->lexicon('discuss.thread_send'));
    $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $modx->lexicon('discuss.print'));
}
$properties['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($actionButtons);

/* thread action buttons */
$actionButtons = array();
if ($modx->user->isAuthenticated()) {
    $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $modx->lexicon('discuss.thread_move'));
    $actionButtons[] = array('url' => '[[~[[++discuss.thread_remove_resource]]]]?thread=[[+id]]', 'text' => $modx->lexicon('discuss.thread_remove'));

    if ($thread->get('locked')) {
        $actionButtons[] = array('url' => '[[~[[++discuss.thread_resource]]]]?thread=[[+id]]&amp;lock=0', 'text' => $modx->lexicon('discuss.thread_unlock'));
    } else {
        $actionButtons[] = array('url' => '[[~[[++discuss.thread_resource]]]]?thread=[[+id]]&amp;lock=1', 'text' => $modx->lexicon('discuss.thread_lock'));
    }
    if ($thread->get('sticky')) {
        $actionButtons[] = array('url' => '[[~[[++discuss.thread_resource]]]]?thread=[[+id]]&amp;sticky=0', 'text' => $modx->lexicon('discuss.thread_unstick'));
    } else {
        $actionButtons[] = array('url' => '[[~[[++discuss.thread_resource]]]]?thread=[[+id]]&amp;sticky=1', 'text' => $modx->lexicon('discuss.thread_stick'));
    }
    $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $modx->lexicon('discuss.thread_merge'));
}
$properties['threadactionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($actionButtons);

/* output */
$modx->setPlaceholder('discuss.error_panel',$discuss->getChunk('disError'));
$modx->setPlaceholder('discuss.thread',$thread->get('title'));

/* set last visited */
if ($discuss->user->profile) {
    $discuss->user->profile->set('thread_last_visited',$thread->get('id'));
    $discuss->user->profile->save();
}

return $discuss->output('thread/view',$properties);

