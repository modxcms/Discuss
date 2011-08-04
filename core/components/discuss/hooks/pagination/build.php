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
 * @package discuss
 * @subpackage hooks
 */
$current = (!empty($_GET['page']) && is_numeric($_GET['page'])) ? intval($_GET['page']) : 1;
$current = $current <= 0 ? 1 : $current;
$limit = $modx->getOption('limit',$scriptProperties,20);
$limit = $limit == 0 ? 1 : $limit;
$count = $modx->getOption('count',$scriptProperties,0);
$total = ceil($count / $limit);

$view = $modx->getOption('view',$scriptProperties,'');
$viewId = $scriptProperties['id'];
$params = $modx->request->getParameters();
unset($params['page']);
unset($params['action']);
unset($params['start']);
$currentResourceUrl = $discuss->request->makeUrl($view,$params);

if ($total <= 1) {
	$pagination = $discuss->getChunk('pagination/paginationActive',array(
	    'text' => $modx->lexicon('discuss.page_one_of_one'),
	    'class' => 'dis-no-pages',
	));
	$pagination = $discuss->getChunk('pagination/PaginationWrapper',array('content' => $pagination));
	$modx->toPlaceholders(array('pagination' => $pagination));
	return;
}

/* Declare variable */
$prev = $current - 1; // Previous page number
$urlPrev = $currentResourceUrl;//.'&page='.$prev;
$next = $current + 1; // Next page number
$urlNext = $currentResourceUrl;//.'&page='.$next;
$n2l = $total - 1; // Next to last page number	
$urlN2l = $currentResourceUrl;//.'&page='.($total - 1);
$urlLast = $currentResourceUrl;//.'&page='.$total;
$truncateText = '<li>...</li>';
$pagination = '';
$list = array();

/* Previous button */
switch ($current) {
	case ($current >= 2):
		$list[] = $discuss->getChunk('pagination/PaginationLink',array('url' => $currentResourceUrl.'&page='.$prev, 'text' => '◄'));
	break;
	default:
		$list[] = $discuss->getChunk('pagination/PaginationActive',array('class' => 'inactive', 'text' => '◄'));
	break;
}
/* If total pages under limit, don't truncate */
if ($total < $limit) {
	for ($i = 1; $i <= $total; $i++) {
		$list[] = ($i == $current)
			? $discuss->getChunk('pagination/PaginationActive', array('class' => 'active', 'text' => $i))
			: $discuss->getChunk('pagination/PaginationLink', array( 'url' => $currentResourceUrl.'&page='.$i, 'text' => $i));
	}

/* Truncate */
} else {
	switch($total){
		/* If the current page near the beginning */
		case $current <= 3:
			/* first page */
			$list[] = ($current == 1)
				? $discuss->getChunk('pagination/PaginationActive', array('class' => 'active', 'text' => 1))
				: $discuss->getChunk('pagination/PaginationLink',array('url' => $currentResourceUrl.'&page='.$prev, 'text' => 1));

			/* And the followings */
			for ($i = 2; $i < 4; $i++) {
				$list[] = ($i == $current)
				? $discuss->getChunk('pagination/PaginationActive', array('class' => 'active', 'text' => $i))
				: $discuss->getChunk('pagination/PaginationLink', array('url' => $currentResourceUrl.'&page='.$i, 'text' => $i));
			}
                
			/* and the remaining pages */
			$list[] = $truncateText;
			$list[] = $discuss->getChunk('pagination/PaginationLink', array('url' => $currentResourceUrl.'&page='.$n2l, 'text' => $n2l));
			$list[] = $discuss->getChunk('pagination/PaginationLink', array('url' => $currentResourceUrl.'&page='.$total, 'text' => $total));
		break;
		/* If current page is in the middle */
		default:
			$list[] = $discuss->getChunk('pagination/PaginationLink', array('url' => $currentResourceUrl.'&page=', 'text' => '1'));
			$list[] = $discuss->getChunk('pagination/PaginationLink', array('url' => $currentResourceUrl.'&page='.(2), 'text' => '2'));
			$list[] = $truncateText;

			for ($i = $current-1; $i <= $current + 1; $i++) {
				$list[] = ($i == $current) ?
					$discuss->getChunk('pagination/PaginationActive',array('class' => 'active', 'text' => $i)) :
					$discuss->getChunk('pagination/PaginationLink',array('url' => $currentResourceUrl.'&page='.$i, 'text' => $i));
			}

			$list[] = $truncateText;
			$list[] = $discuss->getChunk('pagination/PaginationLink', array('url' => $currentResourceUrl.'&page='.$n2l, 'text' => $n2l));
			$list[] = $discuss->getChunk('pagination/PaginationLink', array('url' => $currentResourceUrl.'&page='.$total, 'text' => $total));
		break;
		/* If current page is near the end */
		case $total - $current <= 3:
		
			$list[] = $discuss->getChunk('pagination/PaginationLink', array('url' => $currentResourceUrl.'&page=', 'text' => '1'));
			$list[] = $discuss->getChunk('pagination/PaginationLink', array('url' => $currentResourceUrl.'&page='.(2), 'text' => '2'));
			$list[] = $truncateText;
			
			for ($i = $total - (2 + ($adj * 2)); $i <= $total; $i++) {
				$list[] = ($i == $current) ?
					$discuss->getChunk('pagination/PaginationActive', array('class' => 'active', 'text' => $i)) :
					$discuss->getChunk('pagination/PaginationLink', array('url' => $currentResourceUrl.'&page='.($i), 'text' => $i));
			}
		break;
	}
}

/* Next button */
if ($current == $total) {
	$list[] = $discuss->getChunk('pagination/PaginationActive',array('class' => 'inactive', 'text' => '►'));
} else {
	$list[] = $discuss->getChunk('pagination/PaginationLink',array('url' => $currentResourceUrl.'&page='.($current+1), 'text' => '►'));
}

$list = implode("\n",$list);

/* wrap the pagination */
$pagination = $discuss->getChunk('pagination/PaginationWrapper',array('content' => $list));

/* Send it to the browser */
$modx->setPlaceholder('pagination',$pagination);

return $pagination;