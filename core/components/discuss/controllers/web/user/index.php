<?php
/**
 *
 * @package discuss
 */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/',$scriptProperties);
if (!($discuss instanceof Discuss)) return '';
$discuss->initialize($modx->context->get('key'));
$discuss->setSessionPlace('user:'.$_REQUEST['user']);

$modx->lexicon->load('discuss:user');

/* get default properties */
$cssPostRowCls = $modx->getOption('cssPostRowCls',$scriptProperties,'dis-board-li');
$menuTpl = $modx->getOption('menuTpl',$scriptProperties,'disUserMenu');
$numRecentPosts = $modx->getOption('numRecentPosts',$scriptProperties,10);
$postRowTpl = $modx->getOption('postRowTpl',$scriptProperties,'disPostLi');

/* get user */
if (empty($scriptProperties['user'])) { $modx->sendErrorPage(); }
$user = $modx->getObject('modUser',$scriptProperties['user']);
if ($user == null) { $modx->sendErrorPage(); }

$user->profile = $modx->getObject('disUserProfile',array(
    'user' => $user->get('id'),
));
$placeholders = $user->toArray();
$placeholders = array_merge($user->profile->toArray(),$placeholders);

/* format age */
$age = strtotime($user->profile->get('birthdate'));
$age = round((time() - $age) / 60 / 60 / 24 / 365);
$placeholders['age'] = $age;

/* format gender */
switch ($user->profile->get('gender')) {
    case 'm': $placeholders['gender'] = $modx->lexicon('discuss.male'); break;
    case 'f': $placeholders['gender'] = $modx->lexicon('discuss.female'); break;
    default: $placeholders['gender'] = ''; break;
}

/* get last visited thread */
$lastThread = $user->profile->getOne('ThreadLastVisited');
if ($lastThread) {
    $placeholders = array_merge($placeholders,$lastThread->toArray('lastThread.'));
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
$c->limit($numRecentPosts);
$recentPosts = $modx->getCollection('disPost',$c);
$rps = array();
foreach ($recentPosts as $post) {
    $pa = $post->toArray('',true);
    $pa['class'] = $cssPostRowCls;
    if (empty($pa['thread'])) { $pa['thread'] = $pa['id']; }

    $rps[] = $discuss->getChunk($postRowTpl,$pa);
}
$placeholders['recentPosts'] = implode("\n",$rps);


/* do output */
$placeholders['canEdit'] = $modx->user->get('username') == $user->get('username');
$placeholders['canAccount'] = $modx->user->get('username') == $user->get('username');
$placeholders['usermenu'] = $discuss->getChunk($menuTpl,$placeholders);
$modx->setPlaceholder('discuss.user',$user->get('username'));
return $placeholders;