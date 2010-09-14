<?php
/**
 * Displays the Board
 *
 * @package discuss
 */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/',$scriptProperties);
if (!($discuss instanceof Discuss)) return '';
$discuss->initialize($modx->context->get('key'));
$discuss->setSessionPlace('downloadattachment:'.$_REQUEST['file']);

/* get attachment */
if (empty($_REQUEST['file'])) $modx->sendErrorPage();
$attachment = $modx->getObject('disPostAttachment',$_REQUEST['file']);
if ($attachment == null) $modx->sendErrorPage();

$path = $attachment->getPath();
if (file_exists($path)) {
    $downloads = $attachment->get('downloads');
    $downloads++;
    $attachment->set('downloads',$downloads);
    $attachment->save();

    $modx->sendRedirect($attachment->getUrl());
} else {
    $modx->sendErrorPage();
}

/* output */
return '';

