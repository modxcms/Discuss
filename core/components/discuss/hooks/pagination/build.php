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
 * Build a nice pagination
 *
 * @var modX $modx
 * @var array $scriptProperties
 * @var Discuss $discuss
 * 
 * @package discuss
 * @subpackage hooks
 */

function urlAddPagination($page = null, &$discussSet = null, $actionSet = null, $paramsSet = null) {
    static $discuss = null;
    static $action = null;
    static $params = null;
    
    static $baseUrl = null;
    
    if ($discussSet!==null) {
        $discuss = &$discussSet;
    }
    if ($actionSet!==null) {
        $action = $actionSet;
    }
    if ($paramsSet!==null) {
        $params = $paramsSet;
    }
    
    $paramsTemp = $params;
    
    if ($page!==null) {
        $paramsTemp['page'] = $page;
        return $discuss->request->makeUrl($action, $paramsTemp);
    }
    
    if ($baseUrl!==null) {
        return $baseUrl;
    }
    $baseUrl = $discuss->request->makeUrl($action,$paramsTemp);
    return $baseUrl;
} 

$tplActive = $modx->getOption('tplActive',$scriptProperties,'pagination/paginationActive');
$tplWrapper = $modx->getOption('tplWrapper',$scriptProperties,'pagination/paginationWrapper');
$tplLink = $modx->getOption('tplLink',$scriptProperties,'pagination/paginationLink');
$previousTextTpl = $modx->getOption('tplPreviousText',$scriptProperties,'pagination/paginationPrevious');
$nextTextTpl = $modx->getOption('tplNextText',$scriptProperties,'pagination/paginationNext');
$showPaginationIfOnePage = $modx->getOption('showPaginationIfOnePage',$scriptProperties,true);

if (!isset($scriptProperties['activePage'])) {
    $current = (!empty($_GET['page']) && is_numeric($_GET['page'])) ? intval($_GET['page']) : 1;
    $current = $current <= 0 ? 1 : $current;
} else {
    $current = $scriptProperties['activePage'];
}
$limit = $modx->getOption('limit',$scriptProperties,20);
$limit = $limit == 0 ? 1 : $limit;
$count = $modx->getOption('count',$scriptProperties,0);
$total = ceil($count / $limit);

//$view = $modx->getOption('view',$scriptProperties,'');
//$viewId = $modx->getOption('id', $scriptProperties, '');
$params = $modx->request->getParameters();
$action = $modx->getOption('action',$params,'');
unset($params['page']);
unset($params['action']);
unset($params['start']);

$currentResourceUrl = urlAddPagination(null, $discuss, $action, $params); // Initializing the function

/*if (isset($scriptProperties['baseUrl']) && !empty($scriptProperties['baseUrl'])) {
    $currentResourceUrl = $scriptProperties['baseUrl'];
} else {
    $currentResourceUrl = $discuss->request->makeUrl($view,$params);
}

if (strpos($currentResourceUrl,'?') > 0) {
    $glue = '&';
} else {
    $glue = '?';
}*/

/* get previous/next text */
$previousText = $discuss->getChunk($previousTextTpl,array('url' => $currentResourceUrl));
$nextText = $discuss->getChunk($nextTextTpl,array('url' => $currentResourceUrl));
$includePrevNext = $modx->getOption('includePrevNext', $scriptProperties, true);

if ($total <= 1) {
    if ($showPaginationIfOnePage) {
        $pagination = $discuss->getChunk($tplActive,array(
            'text' => $modx->lexicon('discuss.page_one_of_one'),
            'class' => 'dis-no-pages',
        ));
        $pagination = $discuss->getChunk($tplWrapper,array('content' => $pagination));
        $modx->toPlaceholders(array('pagination' => $pagination));
    }
	return;
}

/* Declare variable */
$prev = $current - 1; // Previous page number
$urlPrev = urlAddPagination(/*$prev*/);//.$glue.'page='.$prev;
$next = $current + 1; // Next page number
$urlNext = urlAddPagination(/*$next*/);//.$glue.'page='.$next;
$n2l = $total - 1; // Next to last page number	
$urlN2l = urlAddPagination(/*($total - 1)*/);//.$glue.'page='.($total - 1);
$urlLast = urlAddPagination(/*$total*/);//.$glue.'page='.$total;
$truncateText = '<li>...</li>';
$pagination = '';
$list = array();

/* Previous button */
if ($includePrevNext) {
    switch ($current) {
        case ($current >= 2):
            $list[] = $discuss->getChunk($tplLink,array('url' => urlAddPagination($prev), 'text' => $previousText));
        break;
        default:
            $list[] = $discuss->getChunk($tplActive,array('class' => 'inactive', 'text' => $previousText));
        break;
    }
}
/* If total pages under limit, don't truncate */
if ($total < $limit) {
	for ($i = 1; $i <= $total; $i++) {
		$list[] = ($i == $current)
			? $discuss->getChunk($tplActive, array('url' => urlAddPagination($current),'class' => 'active', 'text' => $i))
			: $discuss->getChunk($tplLink, array( 'url' => urlAddPagination($i), 'text' => $i));
	}

/* Truncate */
} else {
	switch($total){
		/* If the current page near the beginning */
		case $current <= 4:
			/* first page */
			$list[] = ($current == 1)
				? $discuss->getChunk($tplActive, array('url' => $currentResourceUrl,'class' => 'active', 'text' => 1))
				: $discuss->getChunk($tplLink,array('url' => urlAddPagination($prev), 'text' => 1));

			/* And the followings */
			for ($i = 2; $i < 4; $i++) {
				$list[] = ($i == $current)
				? $discuss->getChunk($tplActive, array('url' => urlAddPagination($current),'class' => 'active', 'text' => $i))
				: $discuss->getChunk($tplLink, array('url' => urlAddPagination($i), 'text' => $i));
			}
            /* Special case for when current is bigger than 3 */
            if ($current >= 3) {
                for ($i = 4; $i <= $current+1; $i++) {
                    $list[] = ($i == $current)
                    ? $discuss->getChunk($tplActive, array('url' => urlAddPagination($current),'class' => 'active', 'text' => $i))
                    : $discuss->getChunk($tplLink, array('url' => urlAddPagination($i), 'text' => $i));
                }
            }
                
			/* and the remaining pages */
			$list[] = $truncateText;
			$list[] = $discuss->getChunk($tplLink, array('url' => urlAddPagination($n2l), 'text' => $n2l));
			$list[] = $discuss->getChunk($tplLink, array('url' => urlAddPagination($total), 'text' => $total));
		break;
		/* If current page is in the middle */
		default:
			$list[] = $discuss->getChunk($tplLink, array('url' => urlAddPagination(1), 'text' => '1'));
			$list[] = $discuss->getChunk($tplLink, array('url' => urlAddPagination(2), 'text' => '2'));
			$list[] = $truncateText;

			for ($i = $current-1; $i <= $current + 1; $i++) {
				$list[] = ($i == $current) ?
					$discuss->getChunk($tplActive,array('url' => urlAddPagination($current),'class' => 'active', 'text' => $i)) :
					$discuss->getChunk($tplLink,array('url' => urlAddPagination($i), 'text' => $i));
			}

			$list[] = $truncateText;
			$list[] = $discuss->getChunk($tplLink, array('url' => urlAddPagination($n2l), 'text' => $n2l));
			$list[] = $discuss->getChunk($tplLink, array('url' => urlAddPagination($total), 'text' => $total));
		break;
		/* If current page is near the end */
		case $total - $current <= 3:
		
			$list[] = $discuss->getChunk($tplLink, array('url' => urlAddPagination(1), 'text' => '1'));
			$list[] = $discuss->getChunk($tplLink, array('url' => urlAddPagination(2), 'text' => '2'));
			$list[] = $truncateText;
			
			for ($i = $current - 1; $i <= $total; $i++) {
				$list[] = ($i == $current) ?
					$discuss->getChunk($tplActive, array('url' => urlAddPagination($current),'class' => 'active', 'text' => $i)) :
					$discuss->getChunk($tplLink, array('url' => urlAddPagination($i), 'text' => $i));
			}
		break;
	}
}

/* Next button */
if ($includePrevNext) {
    if ($current == $total) {
        $list[] = $discuss->getChunk($tplActive,array('class' => 'inactive', 'text' => $nextText));
    } else {
        $list[] = $discuss->getChunk($tplLink,array('url' => urlAddPagination($current+1), 'text' => $nextText));
    }
}

$list = implode("\n",$list);

/* wrap the pagination */
$phs = array(
    'content' => $list,
    'class' => (isset($scriptProperties['outerClass']) && !empty($scriptProperties['outerClass'])) ? $scriptProperties['outerClass'] : '',
);
$pagination = $discuss->getChunk($tplWrapper,$phs);

/* Send it to the browser */
$placeholder = $modx->getOption('toPlaceholder',$scriptProperties,'pagination');
if (!empty($placeholder)) $modx->setPlaceholder($placeholder,$pagination);

return $pagination;
