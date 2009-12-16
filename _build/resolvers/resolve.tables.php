<?php
/**
 * Resolve creating db tables
 *
 * @package discuss
 * @subpackage build
 */
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/';
            $modx->addPackage('discuss',$modelPath,'discuss_');

            $manager = $modx->getManager();

            $manager->createObjectContainer('disBanGroup');
            $manager->createObjectContainer('disBanItem');
            $manager->createObjectContainer('disBoard');
            $manager->createObjectContainer('disBoardClosure');
            $manager->createObjectContainer('disBoardUserGroup');
            $manager->createObjectContainer('disCategory');
            $manager->createObjectContainer('disForumActivity');
            $manager->createObjectContainer('disModerator');
            $manager->createObjectContainer('disPost');
            $manager->createObjectContainer('disPostAttachment');
            $manager->createObjectContainer('disPostClosure');
            $manager->createObjectContainer('disPostRead');
            $manager->createObjectContainer('disReservedUsername');
            $manager->createObjectContainer('disSession');
            $manager->createObjectContainer('disUserFriend');
            $manager->createObjectContainer('disUserGroupProfile');
            $manager->createObjectContainer('disUserModerated');
            $manager->createObjectContainer('disUserNotification');
            $manager->createObjectContainer('disUserProfile');

            break;
        case xPDOTransport::ACTION_UPGRADE:
            break;
    }
}
return true;