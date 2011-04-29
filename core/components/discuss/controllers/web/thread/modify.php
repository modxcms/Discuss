<?php
/**
 *
 * @package discuss
 */
if (empty($scriptProperties['post'])) { $modx->sendErrorPage(); }
$post = $modx->getObject('disPost',$scriptProperties['post']);
if ($post == null) { $modx->sendErrorPage(); }
$discuss->setPageTitle($modx->lexicon('discuss.modify_post_header',array('title' => $post->get('title'))));

/* setup defaults */
$placeholders = $post->toArray();
$placeholders['post'] = $post->get('id');
$placeholders['buttons'] = $discuss->getChunk('disPostButtons',array('buttons_url' => $discuss->config['imagesUrl'].'buttons/'));
$placeholders['message'] = $post->br2nl($placeholders['message']);

/* get thread root */
$thread = $modx->call('disThread', 'fetch', array(&$modx,$post->get('thread')));
if ($thread == null) $modx->sendErrorPage();
$placeholders['thread'] = $thread->get('id');
$placeholders['locked'] = $thread->get('locked');
$placeholders['sticky'] = $thread->get('sticky');

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
$board = $thread->getOne('Board');
if ($board) {
    $board->buildBreadcrumbs(array(array(
        'text' => $modx->lexicon('discuss.modify_post_header',array(
            'post' => $post->get('title'),
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

/* output form to browser */
$modx->regClientHTMLBlock('<script type="text/javascript">
var DISModifyPost = $(function() {
    DIS.config.attachments_max_per_post = '.$placeholders['max_attachments'].';
    DIS.DISModifyPost.init({
        attachments: '.(count($attachments)+1).'
    });
});</script>');
$modx->setPlaceholder('discuss.error_panel',$discuss->getChunk('disError'));
$modx->setPlaceholders($placeholders,'fi.');

return $placeholders;