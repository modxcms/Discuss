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
if (!isset($object) || !isset($object->xpdo)) return false;

$success= true;

$modx =& $object->xpdo;

if (isset($options) && !empty($options['install_resources'])) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:

$modx->log(modX::LOG_LEVEL_INFO,'Installing default Resources...');

$template = !empty($options['template']) ? $options['template'] : 1;

$pages = $modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/');
$pages .= 'elements/pages/';

$resource = $modx->newObject('modResource');
$resource->fromArray(array(
    'pagetitle' => 'Forums',
    'parent' => 0,
    'alias' => !empty($options['forums_alias']) ? $options['forums_alias'] : 'forums',
    'content' => "[[!Discuss]]\n".file_get_contents($pages.'home.tpl'),
    'isfolder' => true,
    'published' => true,
    'hidemenu' => false,
    'cacheable' => true,
    'context_key' => 'web',
    'template' => $template,
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
    'content' => "[[!DiscussBoard]]\n".file_get_contents($pages.'board.tpl'),
    'isfolder' => true,
    'published' => true,
    'cacheable' => true,
    'hidemenu' => true,
    'menuindex' => 0,
    'context_key' => 'web',
    'template' => $template,
));
$children[0]->save();
addMapSetting($modx,'board',$children[0]->get('id'));

    $schildren = array();
    $schildren[0] = $modx->newObject('modResource');
    $schildren[0]->fromArray(array(
        'pagetitle' => 'Thread: [[!+discuss.thread]]',
        'parent' => $children[0]->get('id'),
        'alias' => 'thread',
        'content' => "[[!DiscussThread]]\n".file_get_contents($pages.'thread/view.tpl'),
        'isfolder' => false,
        'published' => true,
        'cacheable' => true,
        'hidemenu' => true,
        'menuindex' => 0,
        'context_key' => 'web',
        'template' => $template,
    ));
    $schildren[0]->save();
    addMapSetting($modx,'thread',$schildren[0]->get('id'));

    $schildren[1] = $modx->newObject('modResource');
    $schildren[1]->fromArray(array(
        'pagetitle' => 'New Thread',
        'parent' => $children[0]->get('id'),
        'alias' => 'new',
        'content' => "[[!DiscussNewThread]]\n".file_get_contents($pages.'thread/new.tpl'),
        'isfolder' => false,
        'published' => true,
        'cacheable' => true,
        'hidemenu' => true,
        'menuindex' => 1,
        'context_key' => 'web',
        'template' => $template,
    ));
    $schildren[1]->save();
    addMapSetting($modx,'new_thread',$schildren[1]->get('id'));

    $schildren[2] = $modx->newObject('modResource');
    $schildren[2]->fromArray(array(
        'pagetitle' => 'Modify Post: [[!+discuss.post]]',
        'parent' => $children[0]->get('id'),
        'alias' => 'modify',
        'content' => "[[!DiscussModifyPost]]\n".file_get_contents($pages.'thread/modify.tpl'),
        'isfolder' => false,
        'published' => true,
        'cacheable' => true,
        'hidemenu' => true,
        'menuindex' => 2,
        'context_key' => 'web',
        'template' => $template,
    ));
    $schildren[2]->save();
    addMapSetting($modx,'modify_post',$schildren[2]->get('id'));

    $schildren[3] = $modx->newObject('modResource');
    $schildren[3]->fromArray(array(
        'pagetitle' => 'Reply to Post: [[!+discuss.post]]',
        'parent' => $children[0]->get('id'),
        'alias' => 'reply',
        'content' => "[[!DiscussReplyPost]]\n".file_get_contents($pages.'thread/reply.tpl'),
        'isfolder' => false,
        'published' => true,
        'cacheable' => true,
        'hidemenu' => true,
        'menuindex' => 3,
        'context_key' => 'web',
        'template' => $template,
    ));
    $schildren[3]->save();
    addMapSetting($modx,'reply_post',$schildren[3]->get('id'));

    $schildren[4] = $modx->newObject('modResource');
    $schildren[4]->fromArray(array(
        'pagetitle' => 'Remove Thread',
        'parent' => $children[0]->get('id'),
        'alias' => 'remove',
        'content' => "[[!DiscussThreadRemove]]\n".file_get_contents($pages.'thread/remove.tpl'),
        'isfolder' => false,
        'published' => true,
        'cacheable' => true,
        'hidemenu' => true,
        'menuindex' => 4,
        'context_key' => 'web',
        'template' => $template,
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
    'content' => "[[!DiscussUser]]\n".file_get_contents($pages.'user/view.tpl'),
    'isfolder' => true,
    'published' => true,
    'cacheable' => true,
    'hidemenu' => true,
    'menuindex' => 1,
    'context_key' => 'web',
    'template' => $template,
));
$children[1]->save();
addMapSetting($modx,'user',$children[1]->get('id'));

    $schildren = array();
    $schildren[0] = $modx->newObject('modResource');
    $schildren[0]->fromArray(array(
        'pagetitle' => 'Edit User: [[!+discuss.user]]',
        'parent' => $children[1]->get('id'),
        'alias' => 'edit',
        'content' => "[[!DiscussUserEdit]]\n".file_get_contents($pages.'user/edit.tpl'),
        'isfolder' => false,
        'published' => true,
        'cacheable' => true,
        'hidemenu' => true,
        'menuindex' => 0,
        'context_key' => 'web',
        'template' => $template,
    ));
    $schildren[0]->save();
    addMapSetting($modx,'user_edit',$schildren[0]->get('id'));

    $schildren[1] = $modx->newObject('modResource');
    $schildren[1]->fromArray(array(
        'pagetitle' => 'Edit User Account: [[!+discuss.user]]',
        'parent' => $children[1]->get('id'),
        'alias' => 'account',
        'content' => "[[!DiscussUserAccount]]\n".file_get_contents($pages.'user/account.tpl'),
        'isfolder' => false,
        'published' => true,
        'hidemenu' => true,
        'menuindex' => 1,
        'context_key' => 'web',
        'template' => $template,
    ));
    $schildren[1]->save();
    addMapSetting($modx,'user_account',$schildren[1]->get('id'));

    $schildren[2] = $modx->newObject('modResource');
    $schildren[2]->fromArray(array(
        'pagetitle' => 'User Notifications: [[!+discuss.user]]',
        'parent' => $children[1]->get('id'),
        'alias' => 'notifications',
        'content' => "[[!DiscussUserNotifications]]\n".file_get_contents($pages.'user/notifications.tpl'),
        'isfolder' => false,
        'published' => true,
        'cacheable' => true,
        'hidemenu' => true,
        'menuindex' => 2,
        'context_key' => 'web',
        'template' => $template,
    ));
    $schildren[2]->save();
    addMapSetting($modx,'user_notifications',$schildren[2]->get('id'));

    $schildren[3] = $modx->newObject('modResource');
    $schildren[3]->fromArray(array(
        'pagetitle' => 'User Stats: [[!+discuss.user]]',
        'parent' => $children[1]->get('id'),
        'alias' => 'stats',
        'content' => "[[!DiscussUserStats]]\n".file_get_contents($pages.'user/stats.tpl'),
        'isfolder' => false,
        'published' => true,
        'cacheable' => true,
        'hidemenu' => true,
        'menuindex' => 3,
        'context_key' => 'web',
        'template' => $template,
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
    'content' => "[[!DiscussLogin]]\n".file_get_contents($pages.'login.tpl'),
    'isfolder' => false,
    'published' => true,
    'cacheable' => true,
    'hidemenu' => false,
    'menuindex' => 2,
    'context_key' => 'web',
    'template' => $template,
));
$children[2]->save();
addMapSetting($modx,'login',$children[2]->get('id'));

$children[3] = $modx->newObject('modResource');
$children[3]->fromArray(array(
    'pagetitle' => 'Register',
    'parent' => $resource->get('id'),
    'alias' => 'register',
    'content' => "[[!DiscussRegister]]\n".file_get_contents($pages.'register.tpl'),
    'isfolder' => true,
    'published' => true,
    'cacheable' => true,
    'hidemenu' => false,
    'menuindex' => 3,
    'context_key' => 'web',
    'template' => $template,
));
$children[3]->save();
addMapSetting($modx,'register',$children[3]->get('id'));

    $schildren = array();
    $schildren[0] = $modx->newObject('modResource');
    $schildren[0]->fromArray(array(
        'pagetitle' => 'Confirm Registration',
        'parent' => $children[3]->get('id'),
        'alias' => 'confirm',
        'content' => "[[!DiscussRegisterConfirm]]\n".file_get_contents($pages.'register-confirm.tpl'),
        'isfolder' => false,
        'published' => true,
        'cacheable' => true,
        'hidemenu' => true,
        'menuindex' => 0,
        'context_key' => 'web',
        'template' => $template,
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
    'content' => "[[!DiscussSearch]]\n".file_get_contents($pages.'search.tpl'),
    'isfolder' => false,
    'published' => true,
    'cacheable' => true,
    'hidemenu' => false,
    'menuindex' => 4,
    'context_key' => 'web',
    'template' => $template,
));
$children[4]->save();
addMapSetting($modx,'search',$children[4]->get('id'));

/* unread posts */
$children[5] = $modx->newObject('modResource');
$children[5]->fromArray(array(
    'pagetitle' => 'Unread Posts',
    'parent' => $resource->get('id'),
    'alias' => 'unread',
    'content' => "[[!DiscussUnreadPosts]]\n".file_get_contents($pages.'unread.tpl'),
    'isfolder' => false,
    'published' => true,
    'cacheable' => true,
    'hidemenu' => true,
    'menuindex' => 5,
    'context_key' => 'web',
    'template' => $template,
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
    'context_key' => 'web',
    'template' => $template,
));
$children[6]->save();
addMapSetting($modx,'connector',$children[6]->get('id'));

//$resource->addMany($children,'Children');
$success = true;

        case xPDOTransport::ACTION_UNINSTALL:

            $success= true;
            break;
    }
}

return $success;