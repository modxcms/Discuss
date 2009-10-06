<?php
/**
 * Login a user to the frontend
 *
 * @package discuss
 * @subpackage processors
 */
$ok = false;

/*
 * TODO: handle case if user has modx account but not discuss account
 * - create user profile
 * - store email from modx profile
 */

$c = $modx->newQuery('disUserProfile');
$c->innerJoin('modUser','User');
$c->where(array(
    'User.username' => $_POST['username'],
));
$profile = $modx->getObject('disUserProfile',$c);
if ($profile == null) $modx->sendUnauthorizedPage();

$status = $profile->get('status');
switch ($status) {
    case DISCUSS_USER_ACTIVE: $ok = true; break;
    case DISCUSS_USER_BANNED:
        $errorOutput = 'Your account has been banned.';
        break;
    case DISCUSS_USER_INACTIVE:
        $errorOutput = 'Your account has been deactivated.';
        break;
    case DISCUSS_USER_UNCONFIRMED:
        $errorOutput = 'Please check your email for confirmation instructions before logging in.';
        break;
    case DISCUSS_USER_AWAITING_MODERATION:
        $errorOutput = 'Your account is awaiting manual approval from a moderator, due to your IP address being flagged as a possible spammer.';
        break;
    default:
        $errorOutput = 'Please register before logging in.';
        break;
}

if ($ok) {
    $oldSessionId = session_id();

    /* send to login processor and handle response */
    $response = $modx->executeProcessor(array(
        'action' => 'login',
        'location' => 'security'
    ));
    if (!empty($response) && is_array($response)) {
        if (!empty($response['success']) && isset($response['object'])) {
            /* remove old session to prevent duplicates */
            $session = $modx->removeObject('disSession',array('id' => $oldSessionId));

            /* update profile; grab by username since ID is not yet stored until page redirect */
            $c = $modx->newQuery('disUserProfile');
            $c->innerJoin('modUser','User');
            $c->where(array(
                'User.username' => $_POST['username'],
            ));
            $profile = $modx->getObject('disUserProfile',$c);
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
                $errorOutput = 'Unknown error logging in.';
            }
            $modx->setPlaceholder('discuss.login_error', $errorOutput);
        }
    }
} else {
    $modx->setPlaceholder('discuss.login_error', $errorOutput);
}

return $errors;