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
$webActions = array(
    'web/post/loadthread',
    'web/post/notify',
    'web/post/pollrefresh',
    'web/post/preview',
    'web/post/remove',
    'web/post/reply',
    'web/post/replyform',
    'web/user/login',
    'web/user/register',
    'web/user/find',
);
if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $webActions)) {
	@session_cache_limiter('public');
    define('MODX_REQP',false);
}
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$disCorePath = $modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/');
require_once $disCorePath.'model/discuss/discuss.class.php';
$modx->discuss = new Discuss($modx);

$modx->lexicon->load('discuss:default');

if (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $webActions)) {
    $version = $modx->getVersionData();
    if (version_compare($version['full_version'],'2.1.1-pl') >= 0) {
        if ($modx->user->hasSessionContext($modx->context->get('key'))) {
            $_SERVER['HTTP_MODAUTH'] = $_SESSION["modx.{$modx->context->get('key')}.user.token"];
        } else {
            $_SESSION["modx.{$modx->context->get('key')}.user.token"] = 0;
            $_SERVER['HTTP_MODAUTH'] = 0;
        }
    } else {
        $_SERVER['HTTP_MODAUTH'] = $modx->site_id;
    }
    $_REQUEST['HTTP_MODAUTH'] = $_SERVER['HTTP_MODAUTH'];
}

/* handle request */
$path = $modx->getOption('processorsPath',$modx->discuss->config,$disCorePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
