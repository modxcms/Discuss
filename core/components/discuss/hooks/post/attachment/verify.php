<?php
/**
 * Discuss
 *
 * Copyright 2010-11 by Shaun McCormick <shaun@modx.com>
 *
 * This file is part of Discuss, a native forum for MODx Revolution.
 *
 * Discuss is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Discuss is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Discuss; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package discuss
 */
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
