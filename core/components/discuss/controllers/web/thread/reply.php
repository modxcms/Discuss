<?php
/**
 * Reply to a current post
 *
 * @package discuss
 */
/* get post */
if (empty($scriptProperties['post'])) { $modx->sendErrorPage(); }
$post = $modx->getObject('disPost',$scriptProperties['post']);
if ($post == null) { $modx->sendErrorPage(); }

/* setup default snippet properties */
$replyPrefix = $modx->getOption('replyPrefix',$scriptProperties,'Re: ');

/* setup placeholders */
$placeholders = $post->toArray();

/* get thread root */
$thread = $post->getOne('Thread');
if ($thread == null) $modx->sendErrorPage();

$placeholders['post'] = $placeholders['id'];
$placeholders['thread'] = $thread->get('id');

/* get board breadcrumb trail */
$c = $modx->newQuery('disBoard');
$c->innerJoin('disBoardClosure','Ancestors');
$c->where(array(
    'Ancestors.descendant' => $post->get('board'),
));
$c->sortby('Ancestors.depth','ASC');
$ancestors = $modx->getCollection('disBoard',$c);

/* build breadcrumbs */
$board = $thread->getOne('Board');
if ($board) {
    $board->buildBreadcrumbs(array(array(
        'text' => $modx->lexicon('discuss.reply_to_post',array(
            'post' => '<a class="active" href="'.$discuss->url.'thread?thread='.$thread->get('id').'">'.$post->get('title').'</a>',
        )),
        'active' => true,
    )),true);
}
$placeholders['trail'] = $board->get('trail');

/* get thread */
$thread = $discuss->hooks->load('post/getthread',array(
    'post' => &$post,
    'thread' => $post->get('thread'),
    'limit' => 5,
));
$placeholders['thread_posts'] = $thread['results'];

/* set max attachment limit */
$placeholders['max_attachments'] = $modx->getOption('discuss.attachments_max_per_post',null,5);
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
$(function() { DIS.config.attachments_max_per_post = '.$placeholders['max_attachments'].'; });
</script>');

/* output form to browser */
$modx->setPlaceholder('discuss.error_panel',$discuss->getChunk('disError'));
$modx->setPlaceholders($placeholders,'fi.');

return $placeholders;