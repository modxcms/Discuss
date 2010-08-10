<?php
/**
 * The edit user page
 *
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));
$modx->lexicon->load('discuss:user');

/* get user */
if (empty($_REQUEST['user'])) { $modx->sendErrorPage(); }
$user = $modx->getObject('modUser',$_REQUEST['user']);
if ($user == null) { $modx->sendErrorPage(); }
$user->profile = $modx->getObject('disUserProfile',array(
    'user' => $user->get('id'),
));

/* get default properties */
$menuTpl = $modx->getOption('menuTpl',$scriptProperties,'disUserMenu');


if (!empty($_POST)) {
    if (empty($_POST['name_first'])) $errors['name_first'] = $modx->lexicon('discuss.user_err_name_first');
    if (empty($_POST['name_last'])) $errors['name_last'] = $modx->lexicon('discuss.user_err_name_last');
    if (empty($_POST['email'])) $errors['email'] = $modx->lexicon('discuss.user_err_email');

    if (empty($errors)) {
        $_POST['signature'] = substr($_POST['signature'],0,$modx->getOption('discuss.max_signature_length',null,2000));

        $user->profile->fromArray($_POST);
        $user->profile->save();
    }
    $modx->toPlaceholders($errors,'error');
}

$placeholders = $user->toArray();
$placeholders = array_merge($user->profile->toArray(),$placeholders);

/* setup genders */
$genders = array('' => '','m' => $modx->lexicon('discuss.male'),'f' => $modx->lexicon('discuss.female'));
$placeholders['genders'] = '';
foreach ($genders as $v => $d) {
    $placeholders['genders'] .= '<option value="'.$v.'"'
        .($user->profile->get('gender') == $v ? ' selected="selected"' : '')
        .'>'.$d.'</option>';
}
unset($genders,$v,$d);

/* do output */
$placeholders['canEdit'] = $modx->user->get('username') == $user->get('username');
$placeholders['canAccount'] = $modx->user->get('username') == $user->get('username');
$modx->setPlaceholder('usermenu',$discuss->getChunk($menuTpl,$placeholders));
$modx->setPlaceholder('discuss.user',$user->get('username'));

return $discuss->output('user/edit',$placeholders);