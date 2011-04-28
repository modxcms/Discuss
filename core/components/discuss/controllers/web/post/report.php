<?php
/**
 * Remove Post page
 *
 * @package discuss
 */
/* get thread root */
$post = $modx->getObject('disPost',$scriptProperties['post']);
if (empty($post)) $modx->sendErrorPage();
$thread = $modx->call('disThread', 'fetch', array(&$modx,$post->get('thread')));
if (empty($thread)) $modx->sendErrorPage();

$discuss->setPageTitle($modx->lexicon('discuss.report_to_mod',array('title' => $thread->get('title'))));

/* get breadcrumb trail */
$placeholders = $post->toArray();
$placeholders['trail'] = $thread->buildBreadcrumbs();

/* process form */
if (!empty($scriptProperties['report-thread'])) {
    $author = $post->getOne('Author');

    /* setup default properties */
    $subject = $modx->getOption('subject',$scriptProperties,$modx->getOption('discuss.email_reported_post_subject',null,'Reported Post: [[+title]]'));
    $subject = str_replace('[[+title]]',$post->get('title'),$subject);
    $tpl = $modx->getOption('tpl',$scriptProperties,$modx->getOption('discuss.email_reported_post_chunk',null,'emails/disReportedEmail'));

    /* build post url */
    $url = $modx->makeUrl($modx->resource->get('id'),'','','full').'thread/?thread='.$post->get('thread').'#dis-post-'.$post->get('id');

    /* setup email properties */
    $emailProperties = array_merge($scriptProperties,$post->toArray());
    $emailProperties['tpl'] = $tpl;
    $emailProperties['title'] = $post->get('title');
    if ($author) {
        $emailProperties['author'] = $author->get('title');
    }
    $emailProperties['reporter'] = $discuss->user->get('username');
    $emailProperties['url'] = $url;
    $emailProperties['forum_title'] = $modx->getOption('discuss.forum_title');
    $emailProperties['message'] = nl2br(strip_tags($scriptProperties['message']));

    /* send reported email */
    $sent = $discuss->sendEmail($discuss->user->get('email'),$discuss->user->get('username'),$subject,$emailProperties);
    unset($emailProperties);

    /* redirect to thread */
    $url = $discuss->url.'thread?thread='.$thread->get('id').'#dis-post-'.$post->get('id');
    $modx->sendRedirect($url);
}

/* output */
$modx->setPlaceholder('discuss.thread',$thread->get('title'));
return $placeholders;
