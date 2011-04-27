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
                $policy = $transport->xpdo->getObject('modAccessPolicy',array(
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
}
return true;