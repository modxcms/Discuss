<?php
/**
 * Display form to post a new thread
 *
 * @package discuss
 */
$discuss->setSessionPlace('newthread:'.$scriptProperties['board']);

if (empty($scriptProperties['board'])) { $modx->sendErrorPage(); }
$board = $modx->getObject('disBoard',$scriptProperties['board']);
if ($board == null) $modx->sendErrorPage();

/* get board breadcrumb trail */
$board->buildBreadcrumbs(array(array(
    'text' => $modx->lexicon('discuss.thread_new'),
    'active' => true,
)),true);

/* setup defaults */
$placeholders = $board->toArray();
$placeholders['buttons'] = $discuss->getChunk('disPostButtons',array('buttons_url' => $discuss->config['imagesUrl'].'buttons/'));

/* set max attachment limit */
$placeholders['max_attachments'] = $modx->getOption('discuss.attachments_max_per_post',null,5);

/* load theme options */
$discuss->config['max_attachments'] = $placeholders['max_attachments'];

/* output form to browser */
$modx->setPlaceholder('discuss.error_panel',$discuss->getChunk('Error'));

return $placeholders;