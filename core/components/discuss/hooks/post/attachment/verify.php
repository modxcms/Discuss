<?php
/**
 * Verify attachments for size, type, validity
 *
 * @package discuss
 * @subpackage hooks
 */
$result = array('attachments' => array(),'errors' => array());
if (empty($scriptProperties['attachments'])) return $result;

$attachments = array();
$maxSize = $modx->getOption('discuss.attachments_max_filesize',null,11509760);
$allowed = $modx->getOption('discuss.attachments_allowed_filetypes',null,'');
$allowed = explode(',',$allowed);
$attachErrors = array();
foreach ($scriptProperties['attachments'] as $key => $file) {
    $hasError = false;

    $idx = str_replace('attachment','',$key);
    if ($file['error'] != 0) {
        $result['errors'][] = $modx->lexicon('discuss.attachment_err_upload',array(
            'idx' => $idx,
        ));
        continue;
    }
    $file['ext'] = pathinfo($file['name'],PATHINFO_EXTENSION);
    if (!in_array($file['ext'],$allowed)) {
        $result['errors'][] = $modx->lexicon('discuss.attachment_bad_type',array('idx' => $idx));
        $hasError = true;
    }

    if ($file['size'] > $maxSize) {
        $result['errors'][] = $modx->lexicon('discuss.attachment_too_large',array(
            'maxSize' => $maxSize,
            'size' => $file['size'],
            'idx' => $idx,
        ));
        $hasError = true;
    }

    $rs = $modx->invokeEvent('OnDiscussAttachmentVerify',array(
        'file' => &$file,
        'idx' => $idx,
        'maxSize' => $maxSize,
        'allowed' => $allowed,
    ));
    $error = $discuss->getEventResult($rs);
    if (!empty($error)) {
        $result['errors'][] = $error;
        $hasError = true;
    }

    if (!$hasError) {
        $result['attachments'][] = $file;
    }
}


return $result;
