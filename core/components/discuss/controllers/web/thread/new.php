<?php
/**
 * Display form to post a new thread
 *
 * @package discuss
 */
$discuss->setSessionPlace('newthread:'.$scriptProperties['board']);
$discuss->setPageTitle($modx->lexicon('discuss.thread_new'));

if (empty($scriptProperties['board'])) { $modx->sendErrorPage(); }
$board = $modx->getObject('disBoard',$scriptProperties['board']);
if ($board == null) $modx->sendErrorPage();

/* ensure user can actually post new */
if (!$board->canPost()) $modx->sendErrorPage();

/* get board breadcrumb trail */
$board->buildBreadcrumbs(array(array(
    'text' => $modx->lexicon('discuss.thread_new'),
    'active' => true,
)),true);

/* setup defaults */
$placeholders = $board->toArray();
$placeholders['buttons'] = $discuss->getChunk('disPostButtons',array('buttons_url' => $discuss->config['imagesUrl'].'buttons/'));

/* perms */
if ($board->canPostLockedThread()) {
    $placeholders['locked'] = !empty($_POST['locked']) ? ' checked="checked"' : '';
    $placeholders['locked_cb'] = '<label class="dis-cb"><input type="checkbox" name="locked" value="1" '.$placeholders['locked'].' />'.$modx->lexicon('discuss.thread_lock').'</label>';
    $placeholders['can_lock'] = true;
}
if ($board->canPostStickyThread()) {
    $placeholders['sticky'] = !empty($_POST['sticky']) ? ' checked="checked"' : '';
    $placeholders['sticky_cb'] = '<label class="dis-cb"><input type="checkbox" name="sticky" value="1" '.$placeholders['sticky'].' />'.$modx->lexicon('discuss.thread_stick').'</label>';
    $placeholders['can_stick'] = true;
}

/* set max attachment limit */
$placeholders['max_attachments'] = $modx->getOption('discuss.attachments_max_per_post',null,5);

/* load theme options */
$discuss->config['max_attachments'] = $placeholders['max_attachments'];

/* output form to browser */
$modx->setPlaceholder('discuss.error_panel',$discuss->getChunk('Error'));

return $placeholders;