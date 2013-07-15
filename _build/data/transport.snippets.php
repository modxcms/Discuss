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
    'name' => 'postHook.DiscussLogin',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/posthook.discusslogin.php'),
),'',true,true);

$snippets[2]= $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
    'id' => 2,
    'name' => 'postHook.DiscussModifyMessage',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/posthook.discussmodifymessage.php'),
),'',true,true);

$snippets[3]= $modx->newObject('modSnippet');
$snippets[3]->fromArray(array(
    'id' => 3,
    'name' => 'postHook.DiscussModifyPost',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/posthook.discussmodifypost.php'),
),'',true,true);

$snippets[4]= $modx->newObject('modSnippet');
$snippets[4]->fromArray(array(
    'id' => 4,
    'name' => 'postHook.DiscussNewMessage',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/posthook.discussnewmessage.php'),
),'',true,true);

$snippets[5]= $modx->newObject('modSnippet');
$snippets[5]->fromArray(array(
    'id' => 5,
    'name' => 'postHook.DiscussNewThread',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/posthook.discussnewthread.php'),
),'',true,true);

$snippets[6]= $modx->newObject('modSnippet');
$snippets[6]->fromArray(array(
    'id' => 6,
    'name' => 'postHook.DiscussReplyMessage',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/posthook.discussreplymessage.php'),
),'',true,true);

$snippets[7]= $modx->newObject('modSnippet');
$snippets[7]->fromArray(array(
    'id' => 7,
    'name' => 'postHook.DiscussReplyPost',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/posthook.discussreplypost.php'),
),'',true,true);

$snippets[8]= $modx->newObject('modSnippet');
$snippets[8]->fromArray(array(
    'id' => 8,
    'name' => 'postHook.DiscussUpdateProfile',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/posthook.discussupdateprofile.php'),
),'',true,true);

$snippets[9]= $modx->newObject('modSnippet');
$snippets[9]->fromArray(array(
    'id' => 9,
    'name' => 'preHook.DiscussLogin',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/prehook.discusslogin.php'),
),'',true,true);

$snippets[10]= $modx->newObject('modSnippet');
$snippets[10]->fromArray(array(
    'id' => 10,
    'name' => 'DiscussUpdateProfileLoader',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussupdateprofileloader.php'),
),'',true,true);

$snippets[11]= $modx->newObject('modSnippet');
$snippets[11]->fromArray(array(
    'id' => 11,
    'name' => 'postHook.DiscussAddBan',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/posthook.discussaddban.php'),
),'',true,true);

$snippets[12]= $modx->newObject('modSnippet');
$snippets[12]->fromArray(array(
    'id' => 12,
    'name' => 'DiscussUrlMaker',
    'description' => '',
    'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.discussurlmaker.php'),
),'',true,true);

return $snippets;