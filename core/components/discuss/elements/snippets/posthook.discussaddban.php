<?php
/**
 * Discuss
 *
 * Copyright 2010-11 by Shaun McCormick <shaun@modx.com>
 *
 * This file is part of Discuss, a native forum for MODx Revolution.
 *
 * Discuss is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Discuss is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Discuss; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package discuss
 */
/**
 * @var modX $modx
 * @var Discuss $discuss
 * @var array $scriptProperties
 *
 * @var fiHooks $hook
 * @var array $fields
 * @var string $submitVar
 *
 * @package discuss
 */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
if (!($discuss instanceof Discuss)) return true;
$modx->lexicon->load('discuss:user');
unset($fields[$submitVar]);

$rptCorePath = $modx->getOption('rampart.core_path',null,$modx->getOption('core_path',null,MODX_CORE_PATH).'components/rampart/');
$modx->addPackage('rampart',$rptCorePath.'model/');
if (!$modx->loadClass('rptBan',$rptCorePath.'model/rampart/')) {
    $hook->addError('reason','Could not load Rampart.');
    return false;
}
$modx->lexicon->load('rampart:default');

/* @var disUser $user get discuss user obj */
$user = $modx->getObject('disUser',$fields['disUser']);
if (empty($user)) {
    $hook->addError('reason','No user found with ID: '.$fields['disUser']);
    return false;
}
$modxUser = $user->getOne('User');

/* @var rptBan $ban create ban obj */
$ban = $modx->newObject('rptBan');
$ban->set('createdon',strftime('%Y-%m-%d %H:%M:%S'));
$ban->set('createdby',$modx->user->get('id'));
$ban->set('active',true);
$ban->set('last_activity',$user->get('last_active'));
$ban->set('reason',$fields['reason']);
$ban->set('notes',$fields['notes']);

/* set optional ban parameters */
if (!empty($fields['cb_ip_range'])) {
    $ban->set('ip',$fields['ip_range']);
}
if (!empty($fields['cb_hostname'])) {
    $ban->set('hostname',$fields['hostname']);
}
if (!empty($fields['cb_email'])) {
    $ban->set('email',$fields['email']);
}
if (!empty($fields['cb_username'])) {
    $ban->set('username',$fields['username']);
}

/* set expiry */
if (!empty($fields['expireson'])) {
    $s = $fields['expireson'] * 24 * 60 * 60;
    $s = strftime(Discuss::DATETIME_FORMATTED,(time() + $s));
    $ban->set('expireson',$s);
}

/* set user status to banned */
$user->set('status',disUser::BANNED);

/* log user out, erase all sessions */
$sessions = $modx->getCollection('disSession',array(
    'user' => $user->get('id'),
));
/** @var disSession $s */
foreach ($sessions as $s) {
    $s->remove();
}
if ($modxUser) {
    $profile = $modxUser->getOne('Profile');
    if ($profile) {
        $sessionId = $profile->get('sessionid');
        $sessions = $modx->getCollection('modSession',array(
            'id' => $sessionId,
        ));
        foreach ($sessions as $s) {
            $s->remove();
        }
    }
}

/* ban user and save */
if ($ban->save() === false) {
    return $hook->addError('reason',$modx->lexicon('rampart.ban_err_save'));
}
$user->save();

/* fire OnDiscussBanUser */
$modx->invokeEvent('OnDiscussBanUser',array(
    'user' => &$user,
    'modUser' => &$modxUser,
    'ban' => &$ban,
));

/* email banned user? */
/* code here */

/* log activity */
$discuss->logActivity('ban_add',$ban->toArray());

/* redirect */
$url = $discuss->request->makeUrl(array('action' => 'user', 'user' => 'ban'),array(
    'u' => $fields['disUser'],
    'success' => true,
));
$modx->sendRedirect($url);
return true;