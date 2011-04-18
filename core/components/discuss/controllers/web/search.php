<?php
/**
 * Search the forums
 *
 * @package discuss
 */
$discuss->setSessionPlace('search');
$discuss->setPageTitle($modx->lexicon('discuss.search_forums'));

/* setup default properties */
$cssSearchResultCls = $modx->getOption('cssSearchResultCls',$scriptProperties,'dis-search-result');
$cssSearchResultParentCls = $modx->getOption('cssSearchResultParentCls',$scriptProperties,'dis-search-parent-result');
$resultRowTpl = $modx->getOption('resultRowTpl',$scriptProperties,'disSearchResult');
$toggle = $modx->getOption('toggle',$scriptProperties,'+');

$limit = !empty($scriptProperties['limit']) ? $scriptProperties['limit'] : $modx->getOption('discuss.threads_per_page',null,20);
$page = !empty($scriptProperties['page']) ? $scriptProperties['page'] : 1;
$page = $page <= 0 ? $page = 1 : $page;
$start = ($page-1) * $limit;
$end = $start+$limit;

/* do search */
$placeholders = array();
if (!empty($scriptProperties['s'])) {
    $string = urldecode(str_replace(array(';',':'),'',strip_tags($scriptProperties['s'])));

    $searchClass = $modx->getOption('discuss.search_class',null,'discuss.search.disSearch');
    $searchClassPath = $modx->getOption('discuss.search_class_path',null,$discuss->config['modelPath']);
    if ($className = $this->modx->loadClass($searchClass,$searchClassPath,true,true)) {
        $discuss->search = new $className($discuss);
    } else {
        $this->modx->log(modX::LOG_LEVEL_ERROR,'Could not load '.$searchClass.' from '.$searchClassPath);
        return array();
    }
    $searchResponse = $discuss->search->run($string,$limit,$start);

    $placeholders['results'] = array();
    $maxScore = 0;
    if (!empty($searchResponse['results'])) {
        foreach ($searchResponse['results'] as $postArray) {
            if ($postArray['score'] > $maxScore) {
                $maxScore = $postArray['score'];
            }

            $postArray['relevancy'] = @number_format(($postArray['score']/$maxScore)*100,0);
            if ($postArray['parent']) {
                $postArray['cls'] = 'dis-search-result dis-result-'.$postArray['thread'];
            } else {
                $postArray['toggle'] = $toggle;
                $postArray['cls'] = 'dis-search-parent-result dis-parent-result-'.$postArray['thread'];
            }
            $postArray['content'] = strip_tags(substr($postArray['content'],0,100));
            $placeholders['results'][] = $discuss->getChunk('disSearchResult',$postArray);
        }
        $placeholders['results'] = implode("\n",$placeholders['results']);
    } else {
        $placeholders['results'] = $modx->lexicon('discuss.search_no_results');
    }
    $placeholders['search'] = $string;
    $placeholders['total'] = number_format($searchResponse['total']);
    $placeholders['start'] = number_format($start+1);
    $placeholders['end'] = number_format($end > $searchResponse['total'] ? $searchResponse['total'] : $end);

    /* get pagination */
    $discuss->hooks->load('pagination/build',array(
        'count' => $searchResponse['total'],
        'view' => 'search',
        'limit' => $limit,
    ));
}

/* get board breadcrumb trail */
$trail = array();
$trail[] = array(
    'url' => $discuss->url,
    'text' => $modx->getOption('discuss.forum_title'),
);
$trail[] = array(
    'text' => $modx->lexicon('discuss.search'),
    'active' => true,
);
$trail = $discuss->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$placeholders['trail'] = $trail;

return $placeholders;