<?php
/**
 *
 * @package discuss
 */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/',$scriptProperties);
if (!($discuss instanceof Discuss)) return '';
$discuss->initialize($modx->context->get('key'));

if (!$modx->user->isAuthenticated($modx->context->get('key'))) {
    $modx->sendUnauthorizedPage();
}

$placeholders = array();

$profile = $modx->getObject('disUserProfile',array(
    'user' => $modx->user->get('id'),
));
if (empty($profile)) $modx->sendUnauthorizedPage();

$status = $profile->get('status');
switch ($status) {
    case disUserProfile::ACTIVE:
        $url = $modx->makeUrl($modx->getOption('discuss.board_list_resource'));
        $modx->sendRedirect($url);
        break;
    case disUserProfile::INACTIVE:
        $placeholders['registration_message'] = $modx->lexicon('discuss.register_conf_deactivated');
        break;
    case disUserProfile::UNCONFIRMED:
        $profile->set('status',disUserProfile::ACTIVE);
        $profile->set('confirmed',1);
        $profile->set('confirmedon',strftime('%Y-%m-%d %H:%M:%S'));
        $profile->set('last_login',strftime('%Y-%m-%d %H:%M:%S'));
        $profile->set('last_active',strftime('%Y-%m-%d %H:%M:%S'));
        $profile->save();
        $url = $modx->makeUrl($modx->getOption('discuss.board_list_resource'));
        $modx->sendRedirect($url);
        break;
    case disUserProfile::BANNED:
        $placeholders['registration_message'] = $modx->lexicon('discuss.register_conf_banned');
        break;
    case disUserProfile::AWAITING_MODERATION:
        $placeholders['registration_message'] = $modx->lexicon('discuss.register_conf_moderated');
        break;
}

/* get board breadcrumb trail */
$trail = '<a href="'.$modx->makeUrl($modx->getOption('discuss.board_list_resource')).'">[[++discuss.forum_title]]</a> / ';
$trail .= $modx->lexicon('discuss.register_confirm');
$placeholders['trail'] = $trail;


/* output */
return $discuss->output('register-confirm',$placeholders);

