<?php
/**
 * User: Dunnock
 * Date: 19.8.2013
 */

$mtime = microtime();
$mtime = explode(' ', $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* override with your own defines here (see build.config.sample.php) */
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');

$disThread = $modx->getTableName('disThread');
$disUser = $modx->getTableName('disUserDeprecated');
$disPost = $modx->getTableName('disPost');
$disProfile = $modx->getTableName('disProfile');
$modUser = $modx->getTableName('modUser');
$participants = $modx->getTableName('disThreadParticipant');

$sql = "UPDATE {$disThread} t SET
t.author_first =
	(SELECT f.user FROM {$disUser} f WHERE f.id = t.author_first),
t.author_last =
	(SELECT l.user FROM {$disUser} l WHERE l.id = t.author_last);
";
$modx->exec($sql);
$modx->log(modX::LOG_LEVEL_INFO, 'Updated author id\'s to threads');

$sql = "UPDATE {$disPost} p SET p.author =
	(SELECT u.user FROM {$disUser} u WHERE u.id = p.author)";
$modx->exec($sql);
$modx->log(modX::LOG_LEVEL_INFO, 'Updated author id\'s to posts');


$sql = "INSERT INTO {$disProfile} (internalKey) VALUES SELECT id FROM {$modUser}";
$modx->exec($sql);
$modx->log(modX::LOG_LEVEL_INFO, 'Insert empty rows per user into discuss user profile table');

$sql = "UPDATE {$participants} p SET p.`user` = (SELECT u.`user` FROM {$disUser} u WHERE u.id = p.`user`)";
$modx->exec($sql);
$modx->log(modX::LOG_LEVEL_INFO, 'Updated thread participant ID\'s to match modUser');


$modx->log(modX::LOG_LEVEL_INFO, 'Loading users back to modUser, modUserProfile and disProfile');
$c = $modx->newQuery('disUserDeprecated');
$c->select(array($modx->getSelectColumns('disUserDeprecated', 'disUserDeprecated', '', array('password', 'salt'), true)));
$disUsers = $modx->getIterator('disUserDeprecated', $c);
$i = 0;
foreach ($disUsers as $u) {
    $user = $modx->getObject('disUser', $u->get('user'));
    $temp = $user->toArray();
    if (empty($temp['createdon']) || $temp['createdon'] == 0) {
        $temp['createdon'] = time();
    } else {
        $temp['createdon'] = strtotime($temp['createdon']);
    }
    if ($temp['gender'] == 'm') {
        $temp['gender'] = 1;
    } else if ($temp['gender'] == 'f') {
        $temp['gender'] = 2;
    } else {
        $temp['gender'] = 0;
    }
    if (empty($u['name_first'])) {
        $name = explode('', $u['fullname']);
        $temp['name_first'] = $name[0];
        $temp['name_last'] = array_splice($name, 1);
    }
    if (empty($u['fullname'])) {
        $temp['fullname'] = implode(" ", array($u['name_last'], $u['name_first']));
    }
    unset($temp['id']);
    $user->fromArray($temp);
    $saved = $user->save();
    if ($saved === false) {
        $modx->log(modX::LOG_LEVEL_INFO, "Could not save user: {$user->get('username')}");
    }
    $i++;
    if (!($i % 50)) {
        $modx->log(modX::LOG_LEVEL_INFO, "{$i} users rewritten. Last user rewritten: {$user->get('username')}");
    }
}

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\nExecution time: {$totalTime}\n");