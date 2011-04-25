<?php
/**
 * The edit user page
 *
 * @package discuss
 */
$modx->lexicon->load('discuss:user');

/* allow external update profile page */
$upResourceId = $modx->getOption('discuss.update_profile_resource_id',null,0);
if (!empty($upResourceId) && $discuss->ssoMode) {
    $url = $modx->makeUrl($upResourceId,'',array('discuss' => 1));
    $modx->sendRedirect($url);
}

/* get user */
if (empty($scriptProperties['user'])) { $modx->sendErrorPage(); }
$user = $modx->getObject('disUser',$scriptProperties['user']);
if ($user == null) { $modx->sendErrorPage(); }
$discuss->setPageTitle($modx->lexicon('discuss.user_edit_header',array('user' => $user->get('username'))));

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

/* setup genders */
$genders = array('' => '','m' => $modx->lexicon('discuss.male'),'f' => $modx->lexicon('discuss.female'));
$placeholders['genders'] = '';
foreach ($genders as $v => $d) {
    $placeholders['genders'] .= '<option value="'.$v.'"'
        .($user->get('gender') == $v ? ' selected="selected"' : '')
        .'>'.$d.'</option>';
}
unset($genders,$v,$d);

/* do output */
$placeholders['canEdit'] = $modx->user->get('username') == $user->get('username');
$placeholders['canAccount'] = $modx->user->get('username') == $user->get('username');
$placeholders['usermenu'] = $discuss->getChunk($menuTpl,$placeholders);
$modx->setPlaceholder('discuss.user',$user->get('username'));

return $placeholders;