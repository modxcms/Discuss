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
    $idx = str_replace('attachment','',$key);
    if ($file['error'] != 0) {
        $result['errors'][] = 'An error occurred while trying to upload attachment '.$idx.'.';
        continue;
    }
    $file['ext'] = pathinfo($file['name'],PATHINFO_EXTENSION);
    if (!in_array($file['ext'],$allowed)) {
        $result['errors'][] = 'Attachment '.$idx.' is not an allowed file type.';
        continue;
    }

    if ($file['size'] > $maxSize) {
        $result['errors'][] = 'Attachment cannot be larger than '.$maxSize.' bytes. Please specify a smaller attachment.';
        continue;
    }
    $result['attachments'][] = $file;
}
return $result;
