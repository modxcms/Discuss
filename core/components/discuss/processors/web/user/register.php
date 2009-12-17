<?php
/**
 * Register a user to a Discuss User account
 *
 * @package discuss
 * @subpackage processors
 */
$modx->lexicon->load('discuss:user');

/* setup default properties */
$emailTpl = $modx->getOption('emailTpl',$scriptProperties,'disRegisterConfirmEmail');
$emailSubject = $modx->getOption('emailSubject',$scriptProperties,'Forum Registration Activation Email');

/* validate fields */
if (!empty($_POST['spam_empty'])) $errors['spam_empty'] = $modx->lexicon('discuss.go_away_spambot');
if (empty($_POST['username'])) $errors['username'] = $modx->lexicon('discuss.register_err_ns_username');
if (empty($_POST['password'])) $errors['password'] = $modx->lexicon('discuss.register_err_ns_password');
if (empty($_POST['password_confirm'])) $errors['password_confirm'] = $modx->lexicon('discuss.register_err_password_confirm');
if ($_POST['password'] != $_POST['password_confirm']) $errors['password_confirm'] = $modx->lexicon('discuss.register_err_password_match');
if (empty($_POST['email'])) $errors['email'] = $modx->lexicon('discuss.register_err_ns_email');

if (empty($_POST['show_email'])) $_POST['show_email'] = 0;

/* check for spammers with stopforumspam.com */
if ($modx->getOption('discuss.use_stopforumspam',null,true)) {
    $className = $modx->loadClass('discuss.spam.stopforumspam.disStopForumSpam',$discuss->config['modelPath'],true,true);
    if (empty($className)) $modx->log(modX::LOG_LEVEL_FATAL,'[Discuss] Could not find disStopForumSpam class!');
    $sfspam = new $className($discuss);
    $spamResult = $sfspam->check($_SERVER['REMOTE_ADDR'],$_POST['email'],$_POST['username']);
    if (!empty($spamResult)) {
        $errors['spam_empty'] = implode(' '.$modx->lexicon('discuss.spam_blocked_by_filter')." \n<br />",$spamResult).$modx->lexicon('discuss.spam_blocked_by_filter')." \n<br />";
    }
}

/* make sure user does not already exist with that username */
$c = $modx->newQuery('modUser');
$c->select('modUser.*,Profile.email AS email');
$c->leftJoin('disUserProfile','Profile','Profile.user = modUser.id');
$c->where(array(
    'username' => $_POST['username'],
));
$user = $modx->getObject('modUser',$c);
if ($user) {
    $email = $user->get('email');
    /* if user exists without discuss profile, check auth */
    if (empty($email)) {
        $userPassword = $user->get('password');
        if ($userPassword != md5($_POST['password'])) {
            $errors['username'] = $modx->lexicon('discuss.user_auth_exists_bad_pass');
        }
    } else {
        $errors['username'] = $modx->lexicon('discuss.user_auth_ae');
    }
}

/* if user might be registering multiple accounts, moderate */
$moderateUser = false;
$ip = $modx->getObject('disUserProfile',array(
    'ip' => $_SERVER['REMOTE_ADDR'],
    'username:!=' => $_POST['username'],
));
if ($ip) { $moderateUser = $modx->lexicon('discuss.duplicate_ip'); }

$sc = $modx->user->getSessionContexts();

if (empty($errors)) {
    /* if no MODx user exists, create one */
    if ($user) {} else {
        $user = $modx->newObject('modUser');
        $user->fromArray($_POST);
        $user->save();
        $user->profile = $modx->newObject('modUserProfile');
        $user->profile->set('internalKey',$user->get('id'));
        $user->profile->fromArray($_POST);
        $user->profile->save();
    }

    /* create Discuss User Profile */
    $profile = $modx->newObject('disUserProfile');
    $profile->fromArray($_POST);
    $profile->set('user',$user->get('id'));
    $profile->set('createdon',strftime('%Y-%m-%d %H:%M:%S'));
    $profile->set('ip',$_SERVER['REMOTE_ADDR']);

    /* if user needs to be moderated, do so here */
    if ($moderateUser !== false) {
        $profile->set('status',disUserProfile::AWAITING_MODERATION);
        $userModed = $modx->newObject('disUserModerated');
        $userModed->set('register_ip',$_SERVER['REMOTE_ADDR']);
        $userModed->set('reason',$moderateUser);
    } else {
        $profile->set('status',disUserProfile::UNCONFIRMED);
    }

    $profile->set('last_login',strftime('%Y-%m-%d %H:%M:%S'));
    $profile->set('last_active',strftime('%Y-%m-%d %H:%M:%S'));
    $profile->set('show_email',$_POST['show_email']);
    $profile->save();

    /* set the email properties */
    $msg = $modx->lexicon('discuss.registered_msg');
    $emailProperties = $user->toArray();
    $emailProperties = array_merge($emailProperties,$profile->toArray());
    $emailProperties['confirm_url'] = $modx->makeUrl($modx->getOption('discuss.confirm_register_resource'));
    $emailProperties['forum_url'] = $modx->makeUrl($modx->getOption('discuss.board_list_resource'));
    $emailProperties['tpl'] = $emailTpl;
    $sent = $discuss->sendEmail($profile->get('email'),$user->get('username'),$emailSubject,$emailProperties);

    /* set message to placeholder */
    $modx->setPlaceholder('discuss.login_error',$msg);
}

return $errors;