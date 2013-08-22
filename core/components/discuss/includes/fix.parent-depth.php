<?php
/* override with your own defines here (see build.config.sample.php) */
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$sql = "SELECT id FROM modx_discuss_threads";
$threads = $modx->query($sql);

foreach ($threads->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "Updating Thread id : {$row['id']}\n";
    $sql = "SELECT id FROM modx_discuss_posts WHERE thread = {$row['id']} ORDER BY id ASC";
    $posts = $modx->query($sql);
    $depth = 0;
    $temp = 0;
    $parent = 0;
    foreach ($posts as $post) {
        if ($depth > 0) {
            $parent = $temp;
        }
        $exec = $modx->exec("UPDATE modx_discuss_posts SET `parent` = {$parent}, `depth` = {$depth} WHERE id = {$post['id']}");
        $temp = $post['id'];
        $depth++;
    }
    echo "Updated {$depth} posts in thread : {$row['id']}\n";
}