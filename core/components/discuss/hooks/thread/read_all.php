<?php
/**
 * Read all unread threads
 * 
 * @package discuss
 * @subpackage hooks
 */
$userId = $discuss->user->get('id');
if (empty($userId)) return false;

$c = $modx->newQuery('disThread');
$c->innerJoin('disPost','FirstPost');
$c->innerJoin('disPost','LastPost');
$c->leftJoin('disThreadRead','Reads',array(
    $modx->getSelectColumns('disThreadRead','Reads','',array('thread')).' = '.$modx->getSelectColumns('disThread','disThread','',array('id')),
    'Reads.user' => $userId,
));
$c->where(array(
    'Reads.id:IS' => null,
));
if (!empty($scriptProperties['lastLogin'])) {
    $c->where(array(
        'LastPost.createdon:>=' => $scriptProperties['lastLogin'],
    ));
}
$c->select($modx->getSelectColumns('disThread','disThread','',array('id','board')));
$c->sortby($modx->getSelectColumns('disThread','disThread','',array('id')));
$c->prepare();
$sql = $c->toSql();
$stmt = $modx->query($sql);
if (!$stmt) { return false; }

$idx = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (!$row) continue;

    $read = $modx->newObject('disThreadRead');
    $read->fromArray(array(
        'thread' => $row['id'],
        'board' => $row['board'],
        'user' => $userId,
    ));
    $idx++;
    $read->save();
}
$stmt->closeCursor();
return true;