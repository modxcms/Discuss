<?php
/**
 * @package discuss
 * @subpackage build
 */
function getSnippetContent($filename) {
    $o = file_get_contents($filename);
    $o = str_replace('<?php','',$o);
    $o = str_replace('?>','',$o);
    $o = trim($o);
    return $o;
}
$snippets = array();

/* step1 snippet */
$snippets[0]= $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
    'id' => 0,
    'name' => 'Discuss',
    'description' => 'Dynamic native, threaded forums.',
    'snippet' => getSnippetContent($sources['root'].'snippet.discuss.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discuss.php';
$snippets[0]->setProperties($properties);
unset($properties);


return $snippets;