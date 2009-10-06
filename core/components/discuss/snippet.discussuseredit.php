<?php
/**
 *
 * @package discuss
 */
require_once  $modx->getOption('core_path').'components/discuss/model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));

if (empty($_REQUEST['user'])) { $modx->sendErrorPage(); }
$user = $modx->getObject('modUser',$_REQUEST['user']);
if ($user == null) { $modx->sendErrorPage(); }

$user->profile = $modx->getObject('disUserProfile',array(
    'user' => $user->get('id'),
));


if (!empty($_POST)) {
    if (empty($_POST['name_first'])) $errors['name_first'] = 'Please enter a valid first name.';
    if (empty($_POST['name_last'])) $errors['name_last'] = 'Please enter a valid last name.';
    if (empty($_POST['email'])) $errors['email'] = 'Please enter a valid email.';

    if (empty($errors)) {
        $_POST['signature'] = substr($_POST['signature'],0,$modx->getOption('discuss.max_signature_length',null,2000));

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

/* do output */
$o = $discuss->getChunk('disUserMenu',$properties);
$o .= $discuss->getChunk('disUserEdit',$properties);
$modx->setPlaceholder('discuss.user',$user->get('username'));

return $discuss->output($o);