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
 * @var xPDOObject $object
 * @package discuss
 * @subpackage build
 */
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_UPGRADE:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/';
            $modx->addPackage('discuss',$modelPath);

            /** @var xPDOManager $manager */
            $manager = $modx->getManager();

            /* Set log level to ERROR */
            $logLevel = $modx->setLogLevel(xPDO::LOG_LEVEL_ERROR);

            $modx->query("ALTER TABLE ".$modx->getTableName('disBoard')." ADD COLUMN `locked` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `status`");
            $modx->query("ALTER TABLE ".$modx->getTableName('disBoard')." ADD INDEX `locked` (`locked`)");

            $modx->query("ALTER TABLE ".$modx->getTableName('disThread')." ADD COLUMN `last_view_ip` TINYINT(1) UNSIGNED NOT NULL DEFAULT '' AFTER `locked`");

            $modx->query("ALTER TABLE ".$modx->getTableName('disCategory')." ADD COLUMN `default_moderators` TEXT AFTER `rank`");
            $modx->query("ALTER TABLE ".$modx->getTableName('disCategory')." ADD COLUMN `default_usergroups` TEXT AFTER `default_moderators`");

            $modx->query("ALTER TABLE ".$modx->getTableName('disPostAttachment')." ADD COLUMN `integrated_id` INT(10) NOT NULL DEFAULT '0' AFTER `downloads`");
            $modx->query("ALTER TABLE ".$modx->getTableName('disPostAttachment')." ADD COLUMN `integrated_data` TEXT AFTER `integrated_id`");

            $modx->query("ALTER TABLE ".$modx->getTableName('disUser')." ADD COLUMN `primary_group` INT(10) NOT NULL DEFAULT '0' AFTER `show_online`");

            $modx->query("ALTER TABLE ".$modx->getTableName('disThread')." ADD COLUMN `title` VARCHAR(255) NOT NULL DEFAULT '' AFTER `board`");
            $modx->query("ALTER TABLE ".$modx->getTableName('disThread')." ADD INDEX `title` (`title`)");

            $modx->query("ALTER TABLE ".$modx->getTableName('disThread')." ADD COLUMN `class_key` VARCHAR(120) NOT NULL DEFAULT 'disThreadDiscussion' AFTER `id`");
            $modx->query("ALTER TABLE ".$modx->getTableName('disThread')." ADD INDEX `class_key` (`class_key`)");

            $modx->query("ALTER TABLE ".$modx->getTableName('disThread')." ADD COLUMN `answered` TINYINT(1) NOT NULL DEFAULT '0' AFTER `locked`");
            $modx->query("ALTER TABLE ".$modx->getTableName('disThread')." ADD INDEX `answered` (`answered`)");
            $modx->query("ALTER TABLE ".$modx->getTableName('disPost')." ADD COLUMN `answer` TINYINT(1) NOT NULL DEFAULT '0' AFTER `allow_replies`");
            $modx->query("ALTER TABLE ".$modx->getTableName('disPost')." ADD INDEX `answer` (`answer`)");

            $modx->query("ALTER TABLE ".$modx->getTableName('disUser')." ADD COLUMN `display_name` VARCHAR(255) NOT NULL DEFAULT '' AFTER `integrated_id`");
            $modx->query("ALTER TABLE ".$modx->getTableName('disUser')." ADD INDEX `display_name` (`display_name`)");
            $modx->query("ALTER TABLE ".$modx->getTableName('disUser')." ADD COLUMN `use_display_name` TINYINT(1) NOT NULL DEFAULT '0' AFTER `display_name`");
            $modx->query("ALTER TABLE ".$modx->getTableName('disUser')." ADD INDEX `use_display_name` (`use_display_name`)");

            $manager->addIndex('disUserFriend','user');
            $manager->addIndex('disUserFriend','friend');

            $manager->addField('disBoard','rtl');
            $manager->addIndex('disBoard','rtl');

            $manager->addField('disThread','post_last_on');
            $manager->addIndex('disThread','post_last_on');

            $manager->addField('disThread','participants');

            /** 2013/01/03: change "message" dbtype from "text" to "mediumtext" for larger posts */
            $manager->alterField('disPost','message');

            /* Set log level back to what it was */
            $modx->setLogLevel($logLevel);

        break;
    }
}
return true;
