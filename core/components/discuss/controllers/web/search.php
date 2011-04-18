<?php
/**
 * Search the forums
 *
 * @package discuss
 */
$discuss->setSessionPlace('search');

/* setup default properties */
$cssSearchResultCls = $modx->getOption('cssSearchResultCls',$scriptProperties,'dis-search-result');
$cssSearchResultParentCls = $modx->getOption('cssSearchResultParentCls',$scriptProperties,'dis-search-parent-result');
$resultRowTpl = $modx->getOption('resultRowTpl',$scriptProperties,'disSearchResult');
$toggle = $modx->getOption('toggle',$scriptProperties,'+');

/* do search */
$placeholders = array();
if (!empty($_REQUEST['s'])) {
    $s = str_replace(array(';',':'),'',strip_tags($_REQUEST['s']));

    $searchClass = $modx->getOption('discuss.search_class',null,'discuss.search.disSearch');
    $searchClassPath = $modx->getOption('discuss.search_class_path',null,$discuss->config['modelPath']);
    if ($className = $this->modx->loadClass($searchClass,$searchClassPath,true,true)) {
        $discuss->search = new $className($discuss);
    } else {
        $this->modx->log(modX::LOG_LEVEL_ERROR,'Could not load '.$searchClass.' from '.$searchClassPath);
        return array();
    }
    $posts = $discuss->search->run($s);

    $placeholders['results'] = array();
    $maxScore = 0;
    if (!empty($posts)) {
        foreach ($posts as $postArray) {
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
    $placeholders['search'] = $s;
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