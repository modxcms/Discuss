<?php
/**
 * Reply to a current post
 *
 * @package discuss
 */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/',$scriptProperties);
if (!($discuss instanceof Discuss)) return '';
$discuss->initialize($modx->context->get('key'));

/* get post */
if (empty($_REQUEST['post'])) { $modx->sendErrorPage(); }
$post = $modx->getObject('disPost',$_REQUEST['post']);
if ($post == null) { $modx->sendErrorPage(); }

/* setup default snippet properties */
$replyPrefix = $modx->getOption('replyPrefix',$scriptProperties,'Re: ');

/* setup placeholders */
$placeholders = $post->toArray();

/* get thread root */
$thread = $post->getThreadRoot();
if ($thread == null) $modx->sendErrorPage();
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
$trail = array();
$trail[] = array(
    'url' => $modx->makeUrl($modx->getOption('discuss.board_list_resource')),
    'text' => $modx->lexicon('discuss.home'),
);
foreach ($ancestors as $ancestor) {
    $trail[] = array(
        'url' => '[[~[[++discuss.board_resource]]? &board=`'.$ancestor->get('id').'`]]',
        'text' => $ancestor->get('name'),
    );
}
$trail[] = array(
    'text' => $modx->lexicon('discuss.reply_to_post',array(
        'post' => '<a class="active" href="[[~[[++discuss.thread_resource]]? &thread=`'.$thread->get('id').'`]]">'.$post->get('title').'</a>',
    )),
    'active' => true,
);
$trail = $modx->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$placeholders['trail'] = $trail;

/* if POST, process new thread request */
if (!empty($_POST)) {
    $modx->toPlaceholders($_POST,'post');
    $result = include $discuss->config['processorsPath'].'web/post/reply.php';
    if ($discuss->processResult($result)) {
        $url = $modx->makeUrl($modx->getOption('discuss.thread_resource'),'',array('thread' => $thread->get('id'))).'#dis-post-'.$result['object']['id'];
        $modx->sendRedirect($url);
    }
} else {
    $modx->setPlaceholder('post.title',$replyPrefix.$post->get('title'));
}

/* get thread */
$placeholders['thread_posts'] = $modx->hooks->load('post/getthread',array(
    'post' => &$post,
    'thread' => &$thread,
));

/* set max attachment limit */
$placeholders['max_attachments'] = $modx->getOption('discuss.attachments_max_per_post',null,5);
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
$(function() { DIS.config.attachments_max_per_post = '.$placeholders['max_attachments'].'; });
</script>');

/* output form to browser */
$modx->regClientStartupScript($discuss->config['jsUrl'].'web/dis.post.reply.js');
$modx->setPlaceholder('discuss.error_panel',$discuss->getChunk('disError'));
$modx->setPlaceholder('discuss.post',$post->get('title'));

return $discuss->output('thread/reply',$placeholders);