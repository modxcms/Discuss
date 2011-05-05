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
    $placeholders['search'] = $string;
    $placeholders['start'] = number_format($start+1);

    if ($discuss->loadSearch()) {
        $searchResponse = $discuss->search->run($string,$limit,$start);

        $placeholders['results'] = array();
        $maxScore = 0;
        if (!empty($searchResponse['results'])) {
            foreach ($searchResponse['results'] as $postArray) {
                if (isset($postArray['score'])) {
                    if ($postArray['score'] > $maxScore) {
                        $maxScore = $postArray['score'];
                    }

                    $postArray['relevancy'] = @number_format(($postArray['score']/$maxScore)*100,0);
                }
                
                if ($postArray['parent']) {
                    $postArray['cls'] = 'dis-search-result dis-result-'.$postArray['thread'];
                } else {
                    $postArray['toggle'] = $toggle;
                    $postArray['cls'] = 'dis-search-parent-result dis-parent-result-'.$postArray['thread'];
                }
                $postArray['message'] = strip_tags($postArray['message']);
                $position = intval(strpos($postArray['message'],$string));
                $length = strlen($postArray['message']);
                if ($position > 0 && $length > $position) {
                    $postArray['message'] = ($position != 0 ? '...' : '').substr($postArray['message'],$position,$position+100).'...';
                } else {
                    $postArray['message'] = substr($postArray['message'],0,100).($length > 100 ? '...' : '');
                }
                if (empty($postArray['url'])) {
                    $postArray['url'] = $discuss->url.'thread/?thread='.$postArray['thread'].'#dis-post-'.$postArray['id'];
                }

                $placeholders['results'][] = $discuss->getChunk('disSearchResult',$postArray);
            }
            $placeholders['results'] = implode("\n",$placeholders['results']);
        } else {
            $placeholders['results'] = $modx->lexicon('discuss.search_no_results');
        }
        $placeholders['total'] = number_format($searchResponse['total']);
        $placeholders['end'] = number_format($end > $searchResponse['total'] ? $searchResponse['total'] : $end);

        /* get pagination */
        $discuss->hooks->load('pagination/build',array(
            'count' => $searchResponse['total'],
            'view' => 'search',
            'limit' => $limit,
        ));
    } else {
        $placeholders['pagination'] = '';
        $placeholders['total'] = 0;
        $placeholders['results'] = 'Could not load search class.';
    }
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