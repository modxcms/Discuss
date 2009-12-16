<?php
/**
 * @package discuss
 * @subpackage build
 */
$chunks = array();

$chunks[0]= $modx->newObject('modChunk');
$chunks[0]->fromArray(array(
    'id' => 0,
    'name' => 'disActiveUserRow',
    'description' => '',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/disactiveuserrow.chunk.tpl'),
    'properties' => '',
),'',true,true);

$chunks[1]= $modx->newObject('modChunk');
$chunks[1]->fromArray(array(
    'id' => 1,
    'name' => 'disBoardLi',
    'description' => '',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/disboardli.chunk.tpl'),
    'properties' => '',
),'',true,true);

/*
$chunks[0]= $modx->newObject('modChunk');
$chunks[0]->fromArray(array(
    'id' => 0,
    'name' => 'dis',
    'description' => '',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/dis.chunk.tpl'),
    'properties' => '',
),'',true,true);
 */

return $chunks;