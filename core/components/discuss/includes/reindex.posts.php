<html>
<head>
    <meta http-equiv="refresh" content="5">
</head>
<body>
<?php
$mtime = microtime();
$mtime = explode(' ', $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

define('IX_OFFSET_DEFAULT', 0);
define('IX_LIMIT_DEFAULT', 1500);
define('FORUMS_RESOURCE_URL', 'forums/');

/* override with your own defines here (see build.config.sample.php) */
include 'config.core.php';
include MODX_CORE_PATH . 'model/modx/modx.class.php';
//$modx = modX::getInstance('foo');
$modx = new modX;
$modx->getVersionData();
if (version_compare($modx->version['full_version'], '2.2.1-pl', '>=')) {
    $modx->initialize('foo', array(
        'transient_context' => true,
        'foo_results' => array(
            'config' => array(
                'session_enabled' => false,
                'log_target' => XPDO_CLI_MODE ? 'ECHO' : 'HTML',
                'log_level' => xPDO::LOG_LEVEL_INFO,
                'debug' => -1,
            ),
            'policies' => array(),
        )
    ));
} else {
    $modx->initialize('foo');
    $modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
    $modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
    $modx->setDebug(-1);
}

$modx->config['discuss.search_class'] = 'disSolrSearch';

$cacheOptions = array(
    xPDO::OPT_CACHE_KEY => 'discuss_indexing'
);

// get last indexed offset
$di = $modx->cacheManager->get('discuss_index', $cacheOptions);
if(empty($di)) {
    $di['offset'] = IX_OFFSET_DEFAULT;
}
$offset = $di['offset'];
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0 ? $_GET['limit'] : IX_LIMIT_DEFAULT;

/* load Discuss */
$modx->addPackage('discuss', $modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
if (!($discuss instanceof Discuss)) return '';

/* setup mem limits */
ini_set('memory_limit','1024M');
set_time_limit(0);
@ob_end_clean();
echo '<pre>';

$discuss->loadRequest();
$discuss->url = FORUMS_RESOURCE_URL;
if (!$discuss->loadSearch()) {
    die('No search class!');
}

/* grab all posts and reindex */
$c = $modx->newQuery('disPost');
$c->innerJoin('disBoard','Board');
$c->innerJoin('disThread','Thread');
$c->innerJoin('disUser','Author');
$c->where(array(
    'Thread.private' => 0,
    'Board.status:!=' => 0,
    //'Thread.answered:=' => 1,
));
$c->select($modx->getSelectColumns('disPost','disPost'));
$c->select(array(
    'Board.name AS board_name',
    'Author.username AS username',
    'Thread.replies AS replies',
    'Thread.users AS users',
    'Thread.private AS private',
));
$c->sortby('id','ASC');
$c->limit($limit, $offset);

$count = 0;
$posts = $modx->getIterator('disPost', $c);
/** @var disPost $post */
foreach ($posts as $post) {
    echo 'Indexing: '.$post->get('title')."\n"; flush();
    $post->index();
}

//$c->prepare(); $sql = $c->toSql();

$perPage = $modx->getOption('discuss.post_per_page',null, 10);
$parser = $modx->getService('disParser','disBBCodeParser',$discuss->config['modelPath'].'discuss/parser/');

/*
$stmt = $modx->query($sql);
if ($stmt) {
    while ($postArray = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $postArray['url'] = $discuss->request->makeUrl('thread', array('thread' => $postArray['thread']));
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
    $di['offset'] = $offset + ++$count;
    $modx->cacheManager->set('discuss_index', $di, $cacheOptions);
}
$discuss->search->commit();

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\nExecution time: {$totalTime}\n");
?>
</body></html>
<?php
@session_write_close();
die();
