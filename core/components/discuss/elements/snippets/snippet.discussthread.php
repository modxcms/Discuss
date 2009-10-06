<?php
/**
 *
 * @package discuss
 */

$mtime = microtime();
$mtime = explode(' ', $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));
$discuss->setSessionPlace('thread:'.$_REQUEST['thread']);

$modx->regClientStartupScript($discuss->config['jsUrl'].'web/dis.thread.js');

/* get thread root */
$c = $modx->newQuery('disPost');
$c->select('disPost.*,
    (SELECT COUNT(*) FROM '.$modx->getTableName('disPostClosure').'
     WHERE
        ancestor = disPost.id
    AND descendant != disPost.id) AS replies
');
$c->where(array(
    'id' => $_REQUEST['thread'],
));
$thread = $modx->getObject('disPost',$c);
if ($thread == null) $modx->sendErrorPage();
unset($c);

/* mark unread if user clicks mark unread */
if (isset($_REQUEST['unread'])) {
    $props = $thread->toArray();
    $props['recurse'] = true;
    $o = $discuss->loadProcessor('web/post/unread',$props);
    if (!$o['success']) {
        $modx->setPlaceholder('discuss.error',$o['message']);
    }
    $boardUrl = $modx->makeUrl($modx->getOption('discuss.board_resource'),'','?board='.$thread->get('board'));
    $modx->sendRedirect($boardUrl);
}

/* mark posts in thread read */
$children = $thread->getDescendants();
foreach ($children as $child) {
    $child->markAsRead();
}


/* get all posts in thread */
$c = $modx->newQuery('disPost');
$c->select('
    disPost.*,
    Descendants.depth AS depth,
    Author.username AS username,
    AuthorProfile.posts AS author_posts,
    AuthorProfile.title AS author_title,
    AuthorProfile.avatar AS author_avatar,
    AuthorProfile.signature AS author_signature,
    AuthorProfile.ip AS author_ip,
    AuthorProfile.location AS author_location,
    AuthorProfile.email AS author_email,
    AuthorProfile.show_email AS author_show_email,
    AuthorProfile.show_online AS author_show_online
');
$c->innerJoin('disPostClosure','Descendants');
$c->innerJoin('disPostClosure','Ancestors');
$c->innerJoin('modUser','Author');
$c->innerJoin('disUserProfile','AuthorProfile');
$c->where(array(
    'Descendants.ancestor' => $thread->get('id'),
));
$c->sortby('disPost.rank','ASC');
$posts = $modx->getCollection('disPost',$c);

/* loop through collected posts */
$plist = array();
$userUrl = $modx->makeUrl($modx->getOption('discuss.user_resource'));
$profileUrl = $modx->getOption('discuss.files_url').'/profile/';
foreach ($posts as $post) {
    $pa = $post->toArray();
    $pa['username'] = '<a href="'.$userUrl.'?user='.$post->get('author').'">'.$post->get('username').'</a>';

    /* set author avatar */
    if (!empty($pa['author_avatar'])) {
        $pa['author_avatar'] = '<img class="dis-post-avatar" alt="'.$pa['author'].'"
            src="'.$profileUrl.$pa['author'].'/'.$pa['author_avatar'].'" />';
    }
    /* check if author wants to show email */
    if (!empty($pa['author_show_email'])) {
        $pa['author_email'] = '<a href="mailto:'.$post->get('author_email').'">'.$modx->lexicon('discuss.email_author').'</a>';
    } else {
        $pa['author_email'] = '';
    }

    /* set depth and check max post depth */
    $pa['class'] = 'dis-board-post dis-depth-'.$pa['depth'];
    if ($post->get('depth') > $modx->getOption('discuss.max_post_depth',null,3)) {
        $pa['class'] .= ' dis-collapsed';
    }
    /* format bbcode */
    $pa['content'] = $post->getContent();

    /* check allowing of custom titles */
    if (!$modx->getOption('discuss.allow_custom_titles',null,true)) {
        $pa['author_title'] = '';
    } else {
        $pa['author_title'] = ' - '.$pa['author_title'];
    }
    $plist[] = $pa;
}

/* parse posts via tree parser */
$discuss->loadTreeParser();
if (count($plist) <= 0) {
    $postsOutput = '<p>'.$modx->lexicon('discuss.thread_no_posts').'</p>';
} else {
    $postsOutput = $discuss->treeParser->parse($plist,'disThreadPost');
}
$thread->set('posts',$postsOutput);
unset($postsOutput,$pa,$plist,$userUrl,$profileUrl);

/* register javascript */
$modx->regClientStartupScript('<script type="text/javascript">
$(function() {
    DISThread.postCount = "'.count($pa).'";
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
$class = 'dis-normal-thread';
$threshold = $modx->getOption('discuss.hot_thread_threshold',null,10);
if ($modx->user->get('id') == $thread->get('author')) {
    $class .= $thread->get('replies') < $threshold ? ' dis-my-normal-thread' : ' dis-my-veryhot-thread';
} else {
    $class .= $thread->get('replies') < $threshold ? '' : ' dis-veryhot-thread';
}
$thread->set('class',$class);
unset($class,$threshold);

/* get viewing users */
$properties['readers'] = $thread->getViewing();

/* action buttons */
$actionButtons = array();
if ($modx->user->isAuthenticated()) {
    $actionButtons[] = array('url' => '[[~[[++discuss.thread_resource]]]]?thread=[[+id]]&unread=1', 'text' => $modx->lexicon('discuss.mark_unread'));
    $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $modx->lexicon('discuss.notify'));
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
$output = $discuss->getChunk('disThread',$properties);
$output .= $discuss->getChunk('disError');
$modx->setPlaceholder('discuss.thread',$thread->get('title'));

/* set last visited */
if ($discuss->user->profile) {
    $discuss->user->profile->set('thread_last_visited',$thread->get('id'));
    $discuss->user->profile->save();
}


return $discuss->output($output);

