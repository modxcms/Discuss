<?php
$mtime = microtime();
$mtime = explode(' ', $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* override with your own defines here (see build.config.sample.php) */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

/* load Discuss */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
if (!($discuss instanceof Discuss)) return '';

/* setup mem limits */
ini_set('memory_limit','1024M');
set_time_limit(0);
@ob_end_clean();
echo '<pre>';

/* load and run importer */
if ($discuss->loadImporter('disSmfImport')) {
    $discuss->import->getConnection();
    $ast = $discuss->import->pdo->query('
        SELECT
            *
        FROM '.$discuss->import->getFullTableName('attachments').'
        WHERE
            `ID_MSG` != 0
    ');
    if (!$ast) return array();
    $aIdx = 0;
    while ($arow = $ast->fetch(PDO::FETCH_ASSOC)) {
        $post = $modx->getObject('disPost',array(
            'integrated_id' => $arow['ID_MSG'],
        ));
        if ($post) {
            $discuss->import->log('Adding attachment: '.$arow['filename']);
            $attachment = $modx->newObject('disPostAttachment');
            $attachment->fromArray(array(
                'post' => $post->get('id'),
                'board' => $post->get('board'),
                'filename' => $arow['filename'],
                'filesize' => $arow['size'],
                'downloads' => $arow['downloads'],
                'integrated_id' => $arow['ID_ATTACH'],
                'integrated_data' => $modx->toJSON(array(
                    'filename' => $arow['filename'],
                    'file_hash' => $arow['file_hash'],
                    'width' => $arow['width'],
                    'height' => $arow['height'],
                    'attachmentType' => $arow['attachmentType'],
                    'ID_MEMBER' => $arow['ID_MEMBER'],
                    'ID_MSG' => $arow['ID_MSG'],
                    'ID_THUMB' => $arow['ID_THUMB'],
                )),
            ));
            $attachment->save();
            $aIdx++;
        }
    }
    $ast->closeCursor();

} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Failed to load Import class.');
}

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\nExecution time: {$totalTime}\n");

exit ();
@session_write_close();
die();

/**
 * disBoard - smf_boards
 * disCategory - smf_categories
 * disPost - smf_messages (smf_topics?)
 * disPostAttachment - smf_attachments
 * modUserGroup/disUserGroupProfile - smf_membergroups
 * disModerator - smf_moderators
 * disUser/modUser - smf_members
 */