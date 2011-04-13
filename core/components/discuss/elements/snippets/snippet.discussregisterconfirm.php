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

$user = $modx->getObject('disUser',array(
    'user' => $modx->user->get('id'),
));
if (empty($user)) $modx->sendUnauthorizedPage();

$status = $user->get('status');
switch ($status) {
    case disUserProfile::ACTIVE:
        $url = $modx->makeUrl($modx->getOption('discuss.board_list_resource'));
        $modx->sendRedirect($url);
        break;
    case disUserProfile::INACTIVE:
        $placeholders['registration_message'] = $modx->lexicon('discuss.register_conf_deactivated');
        break;
    case disUserProfile::UNCONFIRMED:
        $user->set('status',disUserProfile::ACTIVE);
        $user->set('confirmed',1);
        $user->set('confirmedon',strftime('%Y-%m-%d %H:%M:%S'));
        $user->set('last_login',strftime('%Y-%m-%d %H:%M:%S'));
        $user->set('last_active',strftime('%Y-%m-%d %H:%M:%S'));
        $user->save();
        $url = $modx->makeUrl($modx->resource->get('id'));
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
$trail = '<a href="'.$modx->makeUrl($modx->resource->get('id')).'">[[++discuss.forum_title]]</a> / ';
$trail .= $modx->lexicon('discuss.register_confirm');
$placeholders['trail'] = $trail;


/* output */
return $discuss->output('register-confirm',$placeholders);

