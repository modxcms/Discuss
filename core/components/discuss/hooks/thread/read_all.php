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