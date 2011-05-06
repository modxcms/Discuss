<?php
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

/* load Discuss */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
if (!($discuss instanceof Discuss)) return '';

/* setup mem limits */
ini_set('memory_limit','1024M');
set_time_limit(0);
@ob_end_clean();
echo '<pre>';

if (!$discuss->loadSearch()) {
    die('No search class!');
}

$forumsResourceUrl = 'forums/';

/* grab all posts and reindex */
$c = $modx->newQuery('disPost');
$c->innerJoin('disBoard','Board');
$c->innerJoin('disThread','Thread');
$c->innerJoin('disUser','Author');
$c->where(array(
    'Thread.private' => 0,
    'Board.status:!=' => 0,
));
$c->select($modx->getSelectColumns('disPost','disPost'));
$c->select(array(
    'Board.name AS board_name',
    'Author.username AS username',
    'Thread.replies AS replies',
));
$c->sortby('id','ASC');
$c->prepare(); $sql = $c->toSql();

$perPage = $modx->getOption('discuss.post_per_page',null, 10);
$parser = $modx->getService('disParser','disBBCodeParser',$discuss->config['modelPath'].'discuss/parser/');

$stmt = $modx->query($sql);
if ($stmt) {
    while ($postArray = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $postArray['url'] = $forumsResourceUrl.'thread/?thread='.$postArray['thread'];
        $page = 1;
        if ($postArray['replies'] > $perPage) {
            $page = ceil($postArray['replies'] / $perPage);
        }
        if ($page != 1) { $postArray['url'] .= '&page='.$page; }
        $postArray['url'] .= '#dis-post-'.$postArray['id'];

        $message = $parser->parse($postArray['message']);
        $pattern = '|[[\/\!]*?[^\[\]]*?]|si';
        $replace = '';
        $postArray['message'] = preg_replace($pattern, $replace, $message);
        echo 'Indexing: '.$postArray['title']."\n"; flush();
        $discuss->search->index($postArray);
    }
    $stmt->closeCursor();
}


$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\nExecution time: {$totalTime}\n");

@session_write_close();
die();