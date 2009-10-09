<?php
/**
 *
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));
$discuss->setSessionPlace('user:'.$_REQUEST['user']);

if (empty($_REQUEST['user'])) { $modx->sendErrorPage(); }
$user = $modx->getObject('modUser',$_REQUEST['user']);
if ($user == null) { $modx->sendErrorPage(); }

$user->profile = $modx->getObject('disUserProfile',array(
    'user' => $user->get('id'),
));
$properties = $user->toArray();
$properties = array_merge($user->profile->toArray(),$properties);

/* format age */
$age = strtotime($user->profile->get('birthdate'));
$age = round((time() - $age) / 60 / 60 / 24 / 365);
$properties['age'] = $age;

/* format gender */
switch ($user->profile->get('gender')) {
    case 'm': $properties['gender'] = 'Male'; break;
    case 'f': $properties['gender'] = 'Female'; break;
    default: $properties['gender'] = ''; break;
}

/* get last visited thread */
$lastThread = $user->profile->getOne('ThreadLastVisited');
if ($lastThread) {
    $properties = array_merge($properties,$lastThread->toArray('lastThread.'));
}

/* recent posts */
$c = $modx->newQuery('disPost');
$c->select('
    disPost.*,
    Board.name AS board_name,
    Author.username AS author_username,
    (SELECT Post.id FROM '.$modx->getTableName('disPost').' AS Post
        INNER JOIN '.$modx->getTableName('disPostClosure').' AS Ancestors
        ON Ancestors.ancestor = Post.id
     WHERE
         Ancestors.descendant = disPost.id
     AND Ancestors.ancestor != disPost.id
     AND Post.parent = 0
    ) AS thread
');
$c->innerJoin('disBoard','Board');
$c->innerJoin('modUser','Author');
$c->where(array(
    'disPost.author' => $user->get('id'),
));
$c->sortby('createdon','DESC');
$c->limit(10);
$recentPosts = $modx->getCollection('disPost',$c);
$rps = array();
foreach ($recentPosts as $post) {
    $pa = $post->toArray('',true);
    $pa['class'] = 'dis-board-li';
    if (empty($pa['thread'])) { $pa['thread'] = $pa['id']; }

    $rps[] = $discuss->getChunk('disPostLI',$pa);
}
$properties['recentPosts'] = implode("\n",$rps);


/* do output */
$modx->setPlaceholder('discuss.user',$user->get('username'));
$modx->setPlaceholder('usermenu',$discuss->getChunk('disUserMenu',$properties));

return $discuss->output('user/view',$properties);