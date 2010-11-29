<?php
/**
 *
 * @package discuss
 */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/',$scriptProperties);
if (!($discuss instanceof Discuss)) return '';
$discuss->initialize($modx->context->get('key'));

if (empty($_REQUEST['post'])) { $modx->sendErrorPage(); }
$post = $modx->getObject('disPost',$_REQUEST['post']);
if ($post == null) { $modx->sendErrorPage(); }

/* setup defaults */
$placeholders = $post->toArray();

/* get thread root */
$thread = $post->getThreadRoot();
if ($thread == null) $modx->sendErrorPage();
$placeholders['thread'] = $thread->get('id');

/* get attachments for post */
$attachments = $post->getMany('Attachments');
$idx = 1;
$atts = array();
$postAttachmentRowTpl = $modx->getOption('postAttachmentRowTpl',$scriptProperties,'post/disPostEditAttachment');
foreach ($attachments as $attachment) {
    $attachmentArray = $attachment->toArray();
    $attachmentArray['filesize'] = $attachment->convert();
    $attachmentArray['url'] = $attachment->getUrl();
    $attachmentArray['idx'] = $idx;
    $atts[] = $discuss->getChunk($postAttachmentRowTpl,$attachmentArray);
    $idx++;
}
$placeholders['attachments'] = implode("\n",$atts);
$placeholders['max_attachments'] = $modx->getOption('discuss.attachments_max_per_post',null,5);
$placeholders['attachmentCurIdx'] = count($attachments)+1;



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
    'text' => $modx->lexicon('discuss.modify_post_header',array('post' => $post->get('title'))),
    'active' => true,
);
$trail = $modx->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$placeholders['trail'] = $trail;

/* if POST, process new thread request */
if (!empty($_POST)) {
    $modx->toPlaceholders($_POST,'post');
    include $discuss->config['processorsPath'].'web/post/modify.php';
}


/* get thread */
$props = array_merge($scriptProperties,array(
    'post' => &$post,
    'thread' => &$thread,
));
$placeholders['thread_posts'] = $modx->hooks->load('post/getthread',$props);

/* output form to browser */
$modx->regClientScript($discuss->config['jsUrl'].'web/dis.post.modify.js');
$modx->regClientHTMLBlock('<script type="text/javascript">
var DISModifyPost = $(function() {
    DIS.config.attachments_max_per_post = '.$placeholders['max_attachments'].';
    DIS.DISModifyPost.init({
        attachments: '.(count($attachments)+1).'
    });
});</script>');
$modx->setPlaceholder('discuss.error_panel',$discuss->getChunk('disError'));
$modx->setPlaceholder('discuss.post',$post->get('title'));

return $discuss->output('thread/modify',$placeholders);