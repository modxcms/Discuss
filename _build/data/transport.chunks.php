<?php
/**
 * @package discuss
 * @subpackage build
 */
$chunks = array();

$chunks[0]= $modx->newObject('modChunk');
$chunks[0]->fromArray(array(
    'id' => 0,
    'name' => 'disBoard',
    'description' => '',
    'snippet' => file_get_contents($sources['source_core'].'/chunks/disboard.chunk.tpl'),
    'properties' => '',
),'',true,true);


return $chunks;