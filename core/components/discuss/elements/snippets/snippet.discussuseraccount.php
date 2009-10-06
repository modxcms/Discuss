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

$user->profile = $modx->getObject('disUserProfile',array(
    'user' => $user->get('id'),
));

/* save form fields */
if (!empty($_POST)) {
    if (empty($_POST['password']) || $user->get('password') !== md5($_POST['password'])) {
        $errors['password'] = 'Your password was incorrect. Please provide the correct password.';
    }

    if (empty($_POST['username'])) $errors['username'] = 'Please enter a valid username.';
    if (!empty($_POST['password_new'])) {
        if (empty($_POST['password_new'])) $errors['password_new'] = 'Please enter a valid password.';
        if (empty($_POST['password_confirm'])) $errors['password_confirm'] = 'Please confirm your password.';
        if ($_POST['password_new'] != $_POST['password_confirm']) $errors['password_confirm'] = 'Your passwords do not match.';
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
$genders = array('' => '','m' => 'Male','f' => 'Female');
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
$o = $discuss->getChunk('disUserMenu',$properties);
$o .= $discuss->getChunk('disUserAccount',$properties);
$modx->setPlaceholder('discuss.user',$user->get('username'));

return $discuss->output($o);