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
            $modx->addPackage('discuss',$modelPath);

            $manager = $modx->getManager();
            
            //$modx->query("ALTER TABLE ".$modx->getTableName('disUserProfile')." ADD COLUMN `use_gravatar` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `avatar`");
            $modx->query("ALTER TABLE ".$modx->getTableName('disUserProfile')." ADD COLUMN `avatar_service` VARCHAR(255) NOT NULL DEFAULT 'gravatar' AFTER `avatar`");

            break;
        case xPDOTransport::ACTION_UPGRADE:
            break;
    }
}
return true;