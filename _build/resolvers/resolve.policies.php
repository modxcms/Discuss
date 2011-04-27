<?php
/**
 * Auto-assign policies to appropriate User Groups
 *
 * @package discuss
 * @subpackage build
 */
if (!$object->xpdo) return true;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $modx =& $object->xpdo;
        $modelPath = $modx->getOption('quip.core_path',null,$modx->getOption('core_path').'components/quip/').'model/';
        $modx->addPackage('quip',$modelPath);

        $modx->setLogLevel(modX::LOG_LEVEL_ERROR);

        /* assign policy to template */
        $policy = $transport->xpdo->getObject('modAccessPolicy',array(
            'name' => 'QuipModeratorPolicy'
        ));
        if ($policy) {
            $template = $transport->xpdo->getObject('modAccessPolicyTemplate',array('name' => 'QuipModeratorPolicyTemplate'));
            if ($template) {
                $policy->set('template',$template->get('id'));
                $policy->save();
            } else {
                $modx->log(xPDO::LOG_LEVEL_ERROR,'[Quip] Could not find QuipModeratorPolicyTemplate Access Policy Template!');
            }
        } else {
            $modx->log(xPDO::LOG_LEVEL_ERROR,'[Quip] Could not find QuipModeratorPolicy Access Policy!');
        }

        /* assign policy to admin group */
        $policy = $modx->getObject('modAccessPolicy',array('name' => 'QuipModeratorPolicy'));
        $adminGroup = $modx->getObject('modUserGroup',array('name' => 'Administrator'));
        if ($policy && $adminGroup) {
            $access = $modx->getObject('modAccessContext',array(
                'target' => 'mgr',
                'principal_class' => 'modUserGroup',
                'principal' => $adminGroup->get('id'),
                'authority' => 9999,
                'policy' => $policy->get('id'),
            ));
            if (!$access) {
                $access = $modx->newObject('modAccessContext');
                $access->fromArray(array(
                    'target' => 'mgr',
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
}
return true;