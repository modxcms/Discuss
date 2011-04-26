<?php
/**
 *
 * @package discuss
 */
if (empty($scriptProperties['post'])) { $modx->sendErrorPage(); }
$post = $modx->getObject('disPost',$scriptProperties['post']);
if ($post == null) { $modx->sendErrorPage(); }
$discuss->setPageTitle($modx->lexicon('discuss.modify_post_header',array('title' => $post->get('title'))));

$thread = $modx->call('disThread', 'fetch', array(&$modx,$post->get('thread'),disThread::TYPE_MESSAGE));
if (empty($thread)) $modx->sendErrorPage();

/* setup defaults */
$placeholders = $post->toArray();
$placeholders['post'] = $post->get('id');
$placeholders['buttons'] = $discuss->getChunk('disPostButtons',array('buttons_url' => $discuss->config['imagesUrl'].'buttons/'));
$placeholders['participants_usernames'] = $thread->get('participants_usernames');
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

/* get thread */
$thread = $discuss->hooks->load('post/getthread',array(
    'post' => &$post,
    'thread' => $post->get('thread'),
    'limit' => 5,
));
$placeholders['thread_posts'] = $thread['results'];

/* output form to browser */
$modx->regClientHTMLBlock('<script type="text/javascript">
var DISModifyMessage = $(function() {
    DIS.config.attachments_max_per_post = '.$placeholders['max_attachments'].';
    DIS.DISModifyMessage.init({
        attachments: '.(count($attachments)+1).'
    });
});</script>');
$modx->setPlaceholder('discuss.error_panel',$discuss->getChunk('disError'));
$modx->setPlaceholders($placeholders,'fi.');

return $placeholders;