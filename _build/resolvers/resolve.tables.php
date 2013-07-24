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
            $modx->addPackage('discuss',$modelPath);

            $manager = $modx->getManager();

            //$manager->createObjectContainer('disBanGroup');
            //$manager->createObjectContainer('disBanItem');
            $manager->createObjectContainer('disBoard');
            $manager->createObjectContainer('disBoardClosure');
            $manager->createObjectContainer('disBoardUserGroup');
            $manager->createObjectContainer('disCategory');
            $manager->createObjectContainer('disForumActivity');
            $manager->createObjectContainer('disLogActivity');
            $manager->createObjectContainer('disModerator');
            $manager->createObjectContainer('disPost');
            $manager->createObjectContainer('disPostAttachment');
            $manager->createObjectContainer('disPostClosure');
            $manager->createObjectContainer('disReservedUsername');
            $manager->createObjectContainer('disSession');
            $manager->createObjectContainer('disThread');
            $manager->createObjectContainer('disThreadRead');
            $manager->createObjectContainer('disThreadUser');
            $manager->createObjectContainer('disUser');
            $manager->createObjectContainer('disUserFriend');
            $manager->createObjectContainer('disUserGroupProfile');
            $manager->createObjectContainer('disUserModerated');
            $manager->createObjectContainer('disUserNotification');
            $manager->createObjectContainer('disParticipant');
            break;
        case xPDOTransport::ACTION_UPGRADE:
            break;
    }
}
return true;
