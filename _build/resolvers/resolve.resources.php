<?php
/**
 * Auto-adds the Resources for the forums
 *
 * @package discuss
 * @subpackage build
 */
function addMapSetting(&$modx,$name,$id) {
    $setting = $modx->newObject('modSystemSetting');
    $setting->set('key','discuss.'.$name.'_resource');
    $setting->set('value',$id);
    $setting->set('namespace','discuss');
    $setting->set('xtype','textfield');
    $setting->set('area','Resource Map');
    return $setting->save();
}
$success= true;
if ($id= $object->get('id') && isset($options) && !empty($options['install_resources'])) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:


$modx =& $object->xpdo;
$pages = $modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/');
$pages .= 'elements/pages/';

$resource = $modx->newObject('modResource');
$resource->fromArray(array(
    'pagetitle' => 'Forums',
    'parent' => 0,
    'alias' => 'forums',
    'content' => file_get_contents($pages.'home.tpl'),
    'isfolder' => true,
    'published' => true,
    'hidemenu' => false,
));
$resource->save();
addMapSetting($modx,'board_list',$resource->get('id'));

/* board resources */
$children = array();
$children[0] = $modx->newObject('modResource');
$children[0]->fromArray(array(
    'pagetitle' => 'Board: [[!+discuss.board]]',
    'parent' => $resource->get('id'),
    'alias' => 'board',
    'content' => file_get_contents($pages.'board.tpl'),
    'isfolder' => true,
    'published' => true,
    'hidemenu' => true,
    'menuindex' => 0,
));
$children[0]->save();
addMapSetting($modx,'board',$children[0]->get('id'));

    $schildren = array();
    $schildren[0] = $modx->newObject('modResource');
    $schildren[0]->fromArray(array(
        'pagetitle' => 'Thread: [[!+discuss.thread]]',
        'parent' => $children[0]->get('id'),
        'alias' => 'thread',
        'content' => file_get_contents($pages.'thread/view.tpl'),
        'isfolder' => false,
        'published' => true,
        'hidemenu' => true,
        'menuindex' => 0,
    ));
    $schildren[0]->save();
    addMapSetting($modx,'thread',$schildren[0]->get('id'));

    $schildren[1] = $modx->newObject('modResource');
    $schildren[1]->fromArray(array(
        'pagetitle' => 'New Thread',
        'parent' => $children[0]->get('id'),
        'alias' => 'new',
        'content' => file_get_contents($pages.'thread/new.tpl'),
        'isfolder' => false,
        'published' => true,
        'hidemenu' => true,
        'menuindex' => 1,
    ));
    $schildren[1]->save();
    addMapSetting($modx,'new_thread',$schildren[1]->get('id'));

    $schildren[2] = $modx->newObject('modResource');
    $schildren[2]->fromArray(array(
        'pagetitle' => 'Modify Post: [[!+discuss.post]]',
        'parent' => $children[0]->get('id'),
        'alias' => 'modify',
        'content' => file_get_contents($pages.'thread/modify.tpl'),
        'isfolder' => false,
        'published' => true,
        'hidemenu' => true,
        'menuindex' => 2,
    ));
    $schildren[2]->save();
    addMapSetting($modx,'modify_post',$schildren[2]->get('id'));

    $schildren[3] = $modx->newObject('modResource');
    $schildren[3]->fromArray(array(
        'pagetitle' => 'Reply to Post: [[!+discuss.post]]',
        'parent' => $children[0]->get('id'),
        'alias' => 'reply',
        'content' => file_get_contents($pages.'thread/reply.tpl'),
        'isfolder' => false,
        'published' => true,
        'hidemenu' => true,
        'menuindex' => 3,
    ));
    $schildren[3]->save();
    addMapSetting($modx,'reply_post',$schildren[3]->get('id'));

    $schildren[4] = $modx->newObject('modResource');
    $schildren[4]->fromArray(array(
        'pagetitle' => 'Remove Thread',
        'parent' => $children[0]->get('id'),
        'alias' => 'remove',
        'content' => file_get_contents($pages.'thread/remove.tpl'),
        'isfolder' => false,
        'published' => true,
        'hidemenu' => true,
        'menuindex' => 4,
    ));
    $schildren[4]->save();
    addMapSetting($modx,'thread_remove',$schildren[4]->get('id'));

    //$children[0]->addMany($schildren,'Children');

/* user resources */
$children[1] = $modx->newObject('modResource');
$children[1]->fromArray(array(
    'pagetitle' => 'User: [[!+discuss.user]]',
    'parent' => $resource->get('id'),
    'alias' => 'user',
    'content' => file_get_contents($pages.'user.tpl'),
    'isfolder' => true,
    'published' => true,
    'hidemenu' => true,
    'menuindex' => 1,
));
$children[1]->save();
addMapSetting($modx,'user',$children[1]->get('id'));

    $schildren = array();
    $schildren[0] = $modx->newObject('modResource');
    $schildren[0]->fromArray(array(
        'pagetitle' => 'Edit User: [[!+discuss.user]]',
        'parent' => $children[1]->get('id'),
        'alias' => 'edit',
        'content' => file_get_contents($pages.'user/edit.tpl'),
        'isfolder' => false,
        'published' => true,
        'hidemenu' => true,
        'menuindex' => 0,
    ));
    $schildren[0]->save();
    addMapSetting($modx,'user_edit',$schildren[0]->get('id'));

    $schildren[1] = $modx->newObject('modResource');
    $schildren[1]->fromArray(array(
        'pagetitle' => 'Edit User Account: [[!+discuss.user]]',
        'parent' => $children[1]->get('id'),
        'alias' => 'account',
        'content' => file_get_contents($pages.'user/account.tpl'),
        'isfolder' => false,
        'published' => true,
        'hidemenu' => true,
        'menuindex' => 1,
    ));
    $schildren[1]->save();
    addMapSetting($modx,'user_account',$schildren[1]->get('id'));

    $schildren[2] = $modx->newObject('modResource');
    $schildren[2]->fromArray(array(
        'pagetitle' => 'User Notifications: [[!+discuss.user]]',
        'parent' => $children[1]->get('id'),
        'alias' => 'notifications',
        'content' => file_get_contents($pages.'user/notifications.tpl'),
        'isfolder' => false,
        'published' => true,
        'hidemenu' => true,
        'menuindex' => 2,
    ));
    $schildren[2]->save();
    addMapSetting($modx,'user_notifications',$schildren[2]->get('id'));

    $schildren[3] = $modx->newObject('modResource');
    $schildren[3]->fromArray(array(
        'pagetitle' => 'User Stats: [[!+discuss.user]]',
        'parent' => $children[1]->get('id'),
        'alias' => 'stats',
        'content' => file_get_contents($pages.'user/stats.tpl'),
        'isfolder' => false,
        'published' => true,
        'hidemenu' => true,
        'menuindex' => 3,
    ));
    $schildren[3]->save();
    addMapSetting($modx,'user_stats',$schildren[3]->get('id'));
    //$children[1]->addMany($schildren,'Children');

/* login/register */
$children[2] = $modx->newObject('modResource');
$children[2]->fromArray(array(
    'pagetitle' => 'Login',
    'parent' => $resource->get('id'),
    'alias' => 'login',
    'content' => file_get_contents($pages.'login.tpl'),
    'isfolder' => false,
    'published' => true,
    'menuindex' => 2,
));
$children[2]->save();
addMapSetting($modx,'login',$children[2]->get('id'));

$children[3] = $modx->newObject('modResource');
$children[3]->fromArray(array(
    'pagetitle' => 'Register',
    'parent' => $resource->get('id'),
    'alias' => 'register',
    'content' => file_get_contents($pages.'register.tpl'),
    'isfolder' => true,
    'published' => true,
    'hidemenu' => false,
    'menuindex' => 3,
));
$children[3]->save();
addMapSetting($modx,'register',$children[3]->get('id'));

    $schildren = array();
    $schildren[0] = $modx->newObject('modResource');
    $schildren[0]->fromArray(array(
        'pagetitle' => 'Confirm Registration',
        'parent' => $children[3]->get('id'),
        'alias' => 'confirm',
        'content' => file_get_contents($pages.'register-confirm.tpl'),
        'isfolder' => false,
        'published' => true,
        'hidemenu' => true,
        'menuindex' => 0,
    ));
    $schildren[0]->save();
    addMapSetting($modx,'confirm_register',$schildren[0]->get('id'));
    //$children[3]->addMany($schildren,'Children');

/* search */
$children[4] = $modx->newObject('modResource');
$children[4]->fromArray(array(
    'pagetitle' => 'Search',
    'parent' => $resource->get('id'),
    'alias' => 'search',
    'content' => file_get_contents($pages.'saerch.tpl'),
    'isfolder' => false,
    'published' => true,
    'hidemenu' => false,
    'menuindex' => 4,
));
$children[4]->save();
addMapSetting($modx,'search',$children[4]->get('id'));

/* unread posts */
$children[5] = $modx->newObject('modResource');
$children[5]->fromArray(array(
    'pagetitle' => 'Unread Posts',
    'parent' => $resource->get('id'),
    'alias' => 'unread',
    'content' => file_get_contents($pages.'unread.tpl'),
    'isfolder' => false,
    'published' => true,
    'hidemenu' => true,
    'menuindex' => 5,
));
$children[5]->save();
addMapSetting($modx,'unread_posts',$children[5]->get('id'));

/* ajax connector */
$children[6] = $modx->newObject('modResource');
$children[6]->fromArray(array(
    'pagetitle' => 'Discuss Connector',
    'parent' => $resource->get('id'),
    'alias' => 'connector',
    'content' => '[[!DiscussConnector]]',
    'isfolder' => false,
    'cacheable' => false,
    'published' => true,
    'hidemenu' => true,
    'menuindex' => 6,
));
$children[6]->save();
addMapSetting($modx,'connector',$children[6]->get('id'));

//$resource->addMany($children,'Children');
$success = true;

        case xPDOTransport::ACTION_UPGRADE:
        case xPDOTransport::ACTION_UNINSTALL:
            $success= true;
            break;
    }
}

return $success;