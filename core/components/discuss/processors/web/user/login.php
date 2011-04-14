<?php
/**
 * Login a user to the frontend
 *
 * @package discuss
 * @subpackage processors
 */
$modx->lexicon->load('discuss:user');
$ok = false;

/* check for disUser */
if (empty($_POST['username'])) $modx->sendUnauthorizedPage();
$c = $modx->newQuery('disUser');
$c->innerJoin('modUser','User');
$c->where(array(
    'User.username' => $_POST['username'],
));
$profile = $modx->getObject('disUser',$c);
if (empty($profile)) {
    /* couldnt find a disUser. Check to see if there is a MODx user
     * and compare the passwords. If not, send to unauth page.
     */
    $user = $modx->getObject('modUser',array(
        'username' => $_POST['username'],
    ));
    if (empty($user)) $modx->sendUnauthorizedPage();
    if (md5($_POST['password']) != $user->get('password')) $modx->sendUnauthorizedPage();
}

$status = $profile->get('status');
switch ($status) {
    case disUser::ACTIVE: $ok = true; break;
    case disUser::BANNED:
        $errorOutput = $modx->lexicon('discuss.account_banned');
        break;
    case disUser::INACTIVE:
        $errorOutput = $modx->lexicon('discuss.account_deactivated');
        break;
    case disUser::UNCONFIRMED:
        $errorOutput = $modx->lexicon('discuss.account_unconfirmed');
        break;
    case disUser::AWAITING_MODERATION:
        $errorOutput = $modx->lexicon('discuss.account_awaiting_moderation');
        break;
    default:
        $errorOutput = $modx->lexicon('discuss.account_nonexistent');
        break;
}

if ($ok) {
    $oldSessionId = session_id();

    /* send to login processor and handle response */
    $response = $modx->runProcessor('security/login',$_POST);
    if (!empty($response) && is_array($response)) {
        if (!empty($response['success']) && isset($response['object'])) {
            /* remove old session to prevent duplicates */
            $session = $modx->removeObject('disSession',array('id' => $oldSessionId));

            /* update profile; grab by username since ID is not yet stored until page redirect */
            $profile->set('last_login',strftime('%Y-%m-%d %H:%M:%S'));
            $profile->set('last_active',strftime('%Y-%m-%d %H:%M:%S'));
            $profile->save();

            $url = $modx->makeUrl($modx->getOption('discuss.board_list_resource'));
            $modx->sendRedirect($url);
        } else {
            $errorOutput = '';
            if (isset($response['errors']) && !empty($response['errors'])) {
                foreach ($response['errors'] as $error) {
                    $errorOutput .= $error.'<br />';
                }
            } elseif (isset($response['message']) && !empty($response['message'])) {
                $errorOutput = $response['message'];
            } else {
                $errorOutput = $modx->lexicon('discuss.login_err_unknown');
            }
            $modx->setPlaceholder('discuss.login_error', $errorOutput);
        }
    }
} else {
    $modx->setPlaceholder('discuss.login_error', $errorOutput);
}

return $errors;