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

/* setup some flexible mem limits in case of a huge board
 * setting changes can be removed most likely
*/
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
$cSub = $modx->newQuery('disThread');
$cSub->select(array(
    1 => "({$userId})", // Parentheses  trick xPDO to not escape user id as column
    $modx->getSelectColumns('disThread', 'disThread', '', array('board', 'id'))
));

if (!empty($scriptProperties['lastLogin'])) {
    $cSub->where(array('post_last_on:>=' => strtotime($scriptProperties['lastLogin'])));
    $this->modx->log(modX::LOG_LEVEL_ERROR, $scriptProperties['ts']);
    if ($scriptProperties['ts'] !== false) {
        $cSub->where(array('post_last_on:<' => $scriptProperties['ts']));
    }
} else if (!empty($scriptProperties['replies'])) {
    $cSub->innerJoin('disThreadParticipant', 'Participants', array(
        "{$modx->escape('Participants')}.{$modx->escape('user')} = {$userId}",
        "{$modx->escape('Participants')}.{$modx->escape('thread')} = {$modx->escape('disThread')}.{$modx->escape('id')}"
    ));
    $cSub->where(array('author_last:!=' => $userId));
}

$cSub->where(array("{$modx->escape('disThread')}.{$modx->escape('id')} NOT IN ({$disReadSub->toSQL()})",
    'private' => 0));

$cSub->prepare();

$sql .= $cSub->toSQL();

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
return true;
