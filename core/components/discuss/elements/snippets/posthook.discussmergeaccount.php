<?php
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
if (!($discuss instanceof Discuss)) return true;
$modx->lexicon->load('discuss:user');

if (empty($fields['password'])) {
    $hook->addError('password',$modx->lexicon('discuss.user_err_password'));
}
if (empty($fields['username'])) {
    $hook->addError('username',$modx->lexicon('discuss.user_err_username'));
} else {

    if ($fields['username'] == $modx->user->get('username')) {
        $hook->addError('username',$modx->lexicon('discuss.user_err_merge_same'));
    } else {
        $user = $modx->getObject('disUser',array(
            'username' => $fields['username'],
        ));
        if (empty($user)) {
            $hook->addError('username',$modx->lexicon('discuss.user_err_nf'));
        } else {
            $pw = $user->get('password');
            switch ($user->get('source')) {
                case 'smf':
                    $userPassword = @sha1(strtolower($fields['username']) . $fields['password']);
                    if ($userPassword != $pw) {
                        $hook->addError('password',$modx->lexicon('discuss.user_err_password_incorrect'));
                    }
                    break;
                break;
            }
        }
    }
}

if (!empty($hook->errors)) {
    return false;
}

if (!$discuss->user->merge($user)) {
    $hook->addError('username','An error occurred while trying to merge the user.');
}

return true;