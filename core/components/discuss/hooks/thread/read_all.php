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
 * Read all unread threads
 * 
 * @package discuss
 * @subpackage hooks
 */
$userId = $discuss->user->get('id');
if (empty($userId)) return false;

/* setup some flexible mem limits in case of a huge board */
ini_set('memory_limit','512M');
set_time_limit(0);


$disReadSub = $modx->newQuery('disThreadRead');
$disReadSub->setClassAlias('Read');
$disReadSub->select(array($modx->getSelectColumns('disThreadRead', 'Read', '', array('thread'))));
$disReadSub->where(array("{$modx->escape($disReadSub->getAlias())}.user" => $userId));
$disReadSub->prepare();

$disRead = $modx->getTableName('disThreadRead');
$disThread = $modx->getTableName('disThread');
$bindings = array();

$sql = "INSERT INTO {$disRead} ({$modx->getSelectColumns('disThreadRead', '', '', array('user', 'board', 'thread'))}) ";
$sqlSelect = "SELECT {$userId}, {$modx->getSelectColumns('disThread', 'disThread', '', array('board', 'id'))}
    FROM $disThread AS {$modx->escape('disThread')}
    WHERE ";
$where = array();
if (!empty($scriptProperties['lastLogin'])) {
    $where[] = "{$modx->escape('disThread')}.{$modx->escape('post_last_on')} >= :lastlogin";
    $bindings[':lastlogin'] = strtotime($scriptProperties['lastLogin']);
}
$where[] = "{$modx->escape('disThread')}.{$modx->escape('id')} NOT IN ({$disReadSub->toSQL()})";
$sql .= $sqlSelect . implode(' AND ', $where);

$criteria = new xPDOCriteria($modx, $sql);
if ($criteria->prepare()) {
    if (!empty ($bindings)) {
        $criteria->bind($bindings, true, false);
    }
    if (!$criteria->stmt->execute()) {
        $errorInfo= $criteria->stmt->errorInfo();
        $modx->log(xPDO::LOG_LEVEL_ERROR, "Error " . $criteria->stmt->errorCode() . " executing statement:\n" . $criteria->toSQL() . "\n" . print_r($errorInfo, true));
        return false;
    }
}
/*
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
$stmt->closeCursor();*/
return true;
