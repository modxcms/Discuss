<?php
/**
 * Handle updating of profile
 */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
if (!($discuss instanceof Discuss)) return true;
$modx->lexicon->load('discuss:user');

$profile = $modx->user->getOne('Profile');
if (empty($profile)) return '';

$fields = $profile->toArray();

$disUser = $modx->getObject('disUser',array(
    'user' => $modx->user->get('id'),
));
if ($disUser) {
    $fields = array_merge($fields,$disUser->toArray());
    $fields['show_email'] = !empty($fields['show_email']) ? 1 : 0;
    $fields['show_online'] = !empty($fields['show_online']) ? 1 : 0;
}

$forumsResourceId = $modx->getOption('discuss.forums_resource_id',null,0);
if (!empty($_REQUEST['discuss']) && !empty($forumsResourceId)) {
    $url = $modx->makeUrl($forumsResourceId,'','','full');
    $fields['forums_url'] = $url;
}

$placeholderPrefix = $modx->getOption('placeholderPrefix',$scriptProperties,'up');
$modx->toPlaceholders($fields,$placeholderPrefix);
return '';