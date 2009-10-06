<?php
/**
 * Register a user to a Discuss User account
 *
 * @package discuss
 * @subpackage processors
 */
if (!empty($_POST['spam_empty'])) $errors['spam_empty'] = 'Go away spambot.';
if (empty($_POST['username'])) $errors['username'] = 'Please specify a valid username.';
if (empty($_POST['password'])) $errors['password'] = 'Please specify a valid password.';
if (empty($_POST['password_confirm'])) $errors['password_confirm'] = 'Please confirm your password.';
if ($_POST['password'] != $_POST['password_confirm']) $errors['password_confirm'] = 'Your passwords do not match.';
if (empty($_POST['email'])) $errors['email'] = 'Please specify a valid email address.';

if (empty($_POST['show_email'])) $_POST['show_email'] = 0;

/* make sure user does not already exist with that username */
$c = $modx->newQuery('modUser');
$c->select('modUser.*,Profile.email AS email');
$c->leftJoin('disUserProfile','Profile','Profile.user = modUser.id');
$ae = $modx->getObject('modUser',$c);
if ($ae != null) {
    $email = $ae->get('email');
    /* if user exists without discuss profile, check auth */
    if (empty($email)) {
        $user = $modx->getObject('modUser',array(
            'username' => $_POST['username'],
            'password' => md5($_POST['password']),
        ));
        if ($user == null) {
            $errors['username'] = 'You already have a MODx user account, but are
using an incorrect password. Please try again.';
        }
    } else {
        $errors['username'] = 'A user already exists with that name. Please try another.';
    }
}

/* if user might be registering multiple accounts, moderate */
$moderateUser = false;
$ip = $modx->getObject('disUserProfile',array(
    'ip' => $_SERVER['REMOTE_ADDR'],
    'username:!=' => $_POST['username'],
));
if ($ip) { $moderateUser = 'Duplicate IP address.'; }

$sc = $modx->user->getSessionContexts();

if (empty($errors)) {
    if ($user) {} else {
        $user = $modx->newObject('modUser');
        $user->fromArray($_POST);
        //$user->save();
    }

    $profile = $modx->newObject('disUserProfile');
    $profile->fromArray($_POST);
    $profile->set('user',$user->get('id'));
    $profile->set('createdon',strftime('%Y-%m-%d %H:%M:%S'));
    $profile->set('ip',$_SERVER['REMOTE_ADDR']);
    if ($moderateUser !== false) {
        $profile->set('status',DISCUSS_USER_AWAITING_MODERATION);
        $userModed = $modx->newObject('disUserModerated');
        $userModed->set('register_ip',$_SERVER['REMOTE_ADDR']);
        $userModed->set('reason',$moderateUser);
    } else {
        $profile->set('status',DISCUSS_USER_UNCONFIRMED);
    }

    $profile->set('last_login',strftime('%Y-%m-%d %H:%M:%S'));
    $profile->set('last_active',strftime('%Y-%m-%d %H:%M:%S'));
    $profile->set('show_email',$_POST['show_email']);
    //$profile->save();

    /* set the email properties */
    $subject = 'Forum Registration Activation Email';
    $msg = 'You have been registered. A confirmation email will shortly be emailed to you. Please follow the instructions contained in the email to activate your account.';
    $emailProperties = $user->toArray();
    $emailProperties = array_merge($emailProperties,$profile->toArray());
    $emailProperties['confirm_url'] = $modx->makeUrl($modx->getOption('discuss.confirm_register_resource'));
    $emailProperties['forum_url'] = $modx->makeUrl($modx->getOption('discuss.board_list_resource'));
    $emailProperties['tpl'] = 'disRegisterConfirmEmail';
    $sent = $discuss->sendEmail($profile->get('email'),$user->get('username'),$subject,$emailProperties);


    $modx->setPlaceholder('discuss.login_error',$msg);

}

return $errors;