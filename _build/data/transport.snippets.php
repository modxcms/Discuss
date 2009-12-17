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

$snippets[0]= $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
    'id' => 0,
    'name' => 'Discuss',
    'description' => 'Dynamic native, threaded forums.',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discuss.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discuss.php';
$snippets[0]->setProperties($properties);
unset($properties);

$snippets[1]= $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 1,
    'name' => 'DiscussBoard',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussboard.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discussboard.php';
$snippets[1]->setProperties($properties);
unset($properties);

$snippets[2]= $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
    'id' => 2,
    'name' => 'DiscussConnector',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussconnector.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discussconnector.php';
$snippets[2]->setProperties($properties);
unset($properties);

$snippets[3]= $modx->newObject('modSnippet');
$snippets[3]->fromArray(array(
    'id' => 3,
    'name' => 'DiscussLogin',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discusslogin.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discusslogin.php';
$snippets[3]->setProperties($properties);
unset($properties);

$snippets[4]= $modx->newObject('modSnippet');
$snippets[4]->fromArray(array(
    'id' => 4,
    'name' => 'DiscussModifyPost',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussmodifypost.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discussmodifypost.php';
$snippets[4]->setProperties($properties);
unset($properties);

$snippets[5]= $modx->newObject('modSnippet');
$snippets[5]->fromArray(array(
    'id' => 5,
    'name' => 'DiscussNewThread',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussnewthread.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discussnewthread.php';
$snippets[5]->setProperties($properties);
unset($properties);

$snippets[6]= $modx->newObject('modSnippet');
$snippets[6]->fromArray(array(
    'id' => 6,
    'name' => 'DiscussRegister',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussregister.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discussregister.php';
$snippets[6]->setProperties($properties);
unset($properties);

$snippets[7]= $modx->newObject('modSnippet');
$snippets[7]->fromArray(array(
    'id' => 7,
    'name' => 'DiscussReplyPost',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussreplypost.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discussreplypost.php';
$snippets[7]->setProperties($properties);
unset($properties);

$snippets[8]= $modx->newObject('modSnippet');
$snippets[8]->fromArray(array(
    'id' => 8,
    'name' => 'DiscussSearch',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discusssearch.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discusssearch.php';
$snippets[8]->setProperties($properties);
unset($properties);

$snippets[9]= $modx->newObject('modSnippet');
$snippets[9]->fromArray(array(
    'id' => 9,
    'name' => 'DiscussThread',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussthread.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discussthread.php';
$snippets[9]->setProperties($properties);
unset($properties);

$snippets[10]= $modx->newObject('modSnippet');
$snippets[10]->fromArray(array(
    'id' => 10,
    'name' => 'DiscussThreadRemove',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussthreadremove.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discussthreadremove.php';
$snippets[10]->setProperties($properties);
unset($properties);

$snippets[11]= $modx->newObject('modSnippet');
$snippets[11]->fromArray(array(
    'id' => 11,
    'name' => 'DiscussUnreadPosts',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussunreadposts.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discussunreadposts.php';
$snippets[11]->setProperties($properties);
unset($properties);

$snippets[12]= $modx->newObject('modSnippet');
$snippets[12]->fromArray(array(
    'id' => 12,
    'name' => 'DiscussUser',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussuser.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discussuser.php';
$snippets[12]->setProperties($properties);
unset($properties);

$snippets[13]= $modx->newObject('modSnippet');
$snippets[13]->fromArray(array(
    'id' => 13,
    'name' => 'DiscussUserAccount',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussuseraccount.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discussuseraccount.php';
$snippets[13]->setProperties($properties);
unset($properties);

$snippets[14]= $modx->newObject('modSnippet');
$snippets[14]->fromArray(array(
    'id' => 14,
    'name' => 'DiscussUserEdit',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussuseredit.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discussuseredit.php';
$snippets[14]->setProperties($properties);
unset($properties);

$snippets[15]= $modx->newObject('modSnippet');
$snippets[15]->fromArray(array(
    'id' => 15,
    'name' => 'DiscussUserNotifications',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussusernotifications.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discussusernotifications.php';
$snippets[15]->setProperties($properties);
unset($properties);

$snippets[17]= $modx->newObject('modSnippet');
$snippets[17]->fromArray(array(
    'id' => 17,
    'name' => 'DiscussUserStats',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussuserstats.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discussuserstats.php';
$snippets[17]->setProperties($properties);
unset($properties);

$snippets[18]= $modx->newObject('modSnippet');
$snippets[18]->fromArray(array(
    'id' => 18,
    'name' => 'DiscussRecentPosts',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussrecentposts.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discussrecentposts.php';
$snippets[18]->setProperties($properties);
unset($properties);

$snippets[19]= $modx->newObject('modSnippet');
$snippets[19]->fromArray(array(
    'id' => 19,
    'name' => 'DiscussRegisterConfirm',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussregisterconfirm.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.discussregisterconfirm.php';
$snippets[19]->setProperties($properties);
unset($properties);

return $snippets;