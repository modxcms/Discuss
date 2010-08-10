<?php
/**
 *
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));

if (empty($_REQUEST['user'])) { $modx->sendErrorPage(); }
$user = $modx->getObject('modUser',$_REQUEST['user']);
if ($user == null) { $modx->sendErrorPage(); }

$modx->lexicon->load('discuss:user');

/* get default properties */
$menuTpl = $modx->getOption('menuTpl',$scriptProperties,'disUserMenu');


$user->profile = $modx->getObject('disUserProfile',array(
    'user' => $user->get('id'),
));

/* save form fields */
if (!empty($_POST)) {
    if (empty($_POST['password']) || $user->get('password') !== md5($_POST['password'])) {
        $errors['password'] = $modx->lexicon('discuss.user_err_password_incorrect');
    }

    if (empty($_POST['username'])) $errors['username'] = $modx->lexicon('discuss.user_err_username');
    if (!empty($_POST['password_new'])) {
        if (empty($_POST['password_new'])) $errors['password_new'] = $modx->lexicon('discuss.user_err_password');
        if (empty($_POST['password_confirm'])) $errors['password_confirm'] = $modx->lexicon('discuss.user_err_password_confirm');
        if ($_POST['password_new'] != $_POST['password_confirm']) $errors['password_confirm'] = $modx->lexicon('discuss.user_err_password_match');
    }

    if (empty($_POST['show_email'])) { $_POST['show_email'] = 0; }
    if (empty($_POST['show_online'])) { $_POST['show_online'] = 0; }

    if (empty($errors)) {
        $user->set('username',$_POST['username']);
        if (!empty($_POST['password'])) {
            $user->changePassword($_POST['password_current'],$_POST['password']);
        }
        $user->save();
        $user->profile->fromArray($_POST);
        $user->profile->save();
    }
    $modx->toPlaceholders($errors,'error');
}

$properties = $user->toArray();
$properties = array_merge($user->profile->toArray(),$properties);

/* setup genders */
$genders = array('' => '','m' => $modx->lexicon('discuss.male'),'f' => $modx->lexicon('discuss.female'));
$gs = '';
foreach ($genders as $v => $d) {
    $gs .= '<option value="'.$v.'"'
        .($user->profile->get('gender') == $v ? ' selected="selected"' : '')
        .'>'.$d.'</option>';
}
$properties['genders'] = $gs;
unset($genders,$gs,$v,$d);

/* format checkbox settings */
if (!empty($properties['show_email'])) { $properties['show_email'] = ' checked="checked"'; }
if (!empty($properties['show_online'])) { $properties['show_online'] = ' checked="checked"'; }

/* do output */
$properties['canEdit'] = $modx->user->get('username') == $user->get('username');
$properties['canAccount'] = $modx->user->get('username') == $user->get('username');
$modx->setPlaceholder('usermenu',$discuss->getChunk($menuTpl,$properties));
$modx->setPlaceholder('discuss.user',$user->get('username'));

return $discuss->output('user/account',$properties);