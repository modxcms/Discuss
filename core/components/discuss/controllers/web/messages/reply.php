<?php
/**
 * Reply to a current post
 *
 * @package discuss
 */
/* get post */
if (empty($scriptProperties['thread']) && empty($scriptProperties['post'])) $modx->sendErrorPage();
if (!empty($scriptProperties['post']) && empty($scriptProperties['thread'])) {
    $post = $modx->getObject('disPost',$scriptProperties['post']);
    if (empty($post)) $modx->sendErrorPage();
    $scriptProperties['thread'] = $post->get('thread');
}
$thread = $modx->call('disThread', 'fetch', array(&$modx,$scriptProperties['thread'],disThread::TYPE_MESSAGE));
if (empty($thread)) $modx->sendErrorPage();
if (empty($post)) {
    $post = $thread->getOne('FirstPost');
    if (empty($post)) $modx->sendErrorPage();
}
$discuss->setPageTitle($modx->lexicon('discuss.reply_to_post',array('title' => $post->get('title'))));

$author = $post->getOne('Author');

/* setup default snippet properties */
$replyPrefix = $modx->getOption('replyPrefix',$scriptProperties,'Re: ');

/* setup placeholders */
$placeholders = $post->toArray();
$placeholders['participants_usernames'] = $thread->get('participants_usernames');
$placeholders['buttons'] = $discuss->getChunk('disPostButtons',array('buttons_url' => $discuss->config['imagesUrl'].'buttons/'));

$placeholders['post'] = $placeholders['id'];
$placeholders['thread'] = $thread->get('id');

/* get board breadcrumb trail */
$trail = array(array(
    'url' => $discuss->url,
    'text' => $modx->getOption('discuss.forum_title'),
),array(
    'text' => $modx->lexicon('discuss.messages'),
    'url' => $discuss->url.'messages',
),array(
    'text' => $post->get('title'),
    'url' => $discuss->url.'messages/view?message='.$thread->get('id'),
),array(
    'text' => $modx->lexicon('discuss.reply'),
    'active' => true,
));
$placeholders['trail'] = $discuss->hooks->load('breadcrumbs',array(
    'items' => &$trail,
));
$placeholders['is_author'] = ($thread->get('author_first') == $modx->discuss->user->get('id')) ? true : false;

/* get thread */
$thread = $discuss->hooks->load('post/getthread',array(
    'post' => &$post,
    'thread' => &$thread,
    'limit' => 10,
));
$placeholders['thread_posts'] = $thread['results'];

/* quote functionality */
if (empty($_POST) && !empty($scriptProperties['quote'])) {
    $placeholders['message'] = str_replace(array('[',']'),array('&#91;','&#93;'),$post->br2nl($post->get('message')));
    $placeholders['message'] = '[quote author='.$author->get('username').' date='.strtotime($post->get('createdon')).']'.$placeholders['message'].'[/quote]'."\n";
} elseif (empty($_POST) && empty($scriptProperties['quote'])) {
    $placeholders['message'] = '';
}

/* default values */
if (empty($_POST)) {
    $placeholders['title'] = $replyPrefix.$placeholders['title'];
}

/* set max attachment limit */
$placeholders['max_attachments'] = $modx->getOption('discuss.attachments_max_per_post',null,5);
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
$(function() { DIS.config.attachments_max_per_post = '.$placeholders['max_attachments'].'; });
</script>');

/* output form to browser */
$modx->setPlaceholder('discuss.error_panel',$discuss->getChunk('disError'));
$modx->setPlaceholders($placeholders,'fi.');

return $placeholders;