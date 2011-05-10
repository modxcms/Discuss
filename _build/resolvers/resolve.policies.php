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
 * Auto-assign policies to appropriate User Groups
 *
 * @package discuss
 * @subpackage build
 */
if (!$object->xpdo) return true;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        $modx =& $object->xpdo;
        $modelPath = $modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/';
        $modx->addPackage('discuss',$modelPath);

        $modx->setLogLevel(modX::LOG_LEVEL_ERROR);

        /* assign policies to DiscussTemplate ACL Policy Template */
        $policies = array(
            'Discuss Administrator Policy',
            'Discuss Moderator Policy',
        );
        $template = $modx->getObject('modAccessPolicyTemplate',array('name' => 'DiscussTemplate'));
        if ($template) {
            foreach ($policies as $policyName) {
                $policy = $modx->getObject('modAccessPolicy',array(
                    'name' => $policyName,
                ));
                if ($policy) {
                    $policy->set('template',$template->get('id'));
                    $policy->save();
                } else {
                    $modx->log(xPDO::LOG_LEVEL_ERROR,'[Discuss] Could not find "'.$policyName.'" Access Policy!');
                }
            }
        } else {
            $modx->log(xPDO::LOG_LEVEL_ERROR,'[Discuss] Could not find DiscussTemplate Access Policy Template!');
        }


        /* assign policy to admin group */
        $policy = $modx->getObject('modAccessPolicy',array('name' => 'Discuss Administrator Policy'));
        $adminGroup = $modx->getObject('modUserGroup',array('name' => 'Administrator'));
        if ($policy && $adminGroup) {
            $access = $modx->getObject('modAccessContext',array(
                'target' => 'web',
                'principal_class' => 'modUserGroup',
                'principal' => $adminGroup->get('id'),
                'authority' => 9999,
                'policy' => $policy->get('id'),
            ));
            if (!$access) {
                $access = $modx->newObject('modAccessContext');
                $access->fromArray(array(
                    'target' => 'web',
                    'principal_class' => 'modUserGroup',
                    'principal' => $adminGroup->get('id'),
                    'authority' => 9999,
                    'policy' => $policy->get('id'),
                ));
                $access->save();
            }
        }
        $modx->setLogLevel(modX::LOG_LEVEL_INFO);
        break;
    case xPDOTransport::ACTION_UPGRADE:
        break;
}
return true;