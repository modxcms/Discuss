<?php
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/',$scriptProperties);
if (!($discuss instanceof Discuss)) return '';
$discuss->initialize($modx->context->get('key'));

require dirname(__FILE__).'/systems.inc.php';
//$smfPassMethod = @sha1(strtolower($username) . $password);

/**
 * disBoard - smf_boards
 * disCategory - smf_categories
 * disPost - smf_messages (smf_topics?)
 * disPostAttachment - smf_attachments
 * modUserGroup/disUserGroupProfile - smf_membergroups
 * disModerator - smf_moderators
 * disUser/modUser - smf_members
 */

try {
    $pdo = new PDO($systems['smf']['dsn'], $systems['smf']['username'], $systems['smf']['password']);
} catch (PDOException $e) {
    return 'Connection failed: ' . $e->getMessage();
}

echo '<pre>';

$stmt = $pdo->query('SELECT * FROM smf_categories LIMIT 10');
if ($stmt) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row) {
            print_r($row);
        }
    }
    $stmt->closeCursor();
}

return 'Done.';