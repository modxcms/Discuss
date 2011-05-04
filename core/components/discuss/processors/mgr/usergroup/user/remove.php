<?php
/**
 * @package discuss
 * @subpackage processors
 */
$response = $modx->runProcessor('security/group/user/remove',array(
    'user' => $scriptProperties['user'],
    'usergroup' => $scriptProperties['usergroup'],
));
if ($response->isError()) {
    return $modx->error->failure($response->getMessage());
}
return $modx->error->success('',$response->getObject());