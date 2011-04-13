<?php
/**
 * Build a nice pagination
 * @package discuss
 * @subpackage hooks
 */
function addParam() {
    return '';
}
$param = $modx->getOption('param',$scriptProperties,'page');
$current = (!empty($_GET[$param]) && is_numeric($_GET[$param])) ? $_GET[$param] : 1 ;
$limit = $modx->getOption('limit',$scriptProperties,20);
$count = $modx->getOption('count',$scriptProperties,0);
$total = ceil($count / 10);
$total = 10;

$view = $modx->getOption('view',$scriptProperties,'');
$viewId = $scriptProperties['id'];
$params = $modx->request->getParameters();
unset($params[$param]);
$currentResourceUrl = $modx->makeUrl($modx->resource->get('id'),'',$params);

if ($total <= 1) {
	$pagination = '<div class="inactive">Page 1 of 1</div>';
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
		$list[] = $discuss->getChunk('pagination/PaginationLink',array('url' => $urlPrev, 'text' => '◄'));
	break;
	default:
		$list[] = $discuss->getChunk('pagination/PaginationActive',array('class' => 'inactive', 'text' => '◄'));
	break;
}

/* If total pages under 10, don't truncate */
if ($total < 10) {
	for ($i = 1; $i <= $total; $i++) {
		$list[] = ($i == $current)
			? $discuss->getChunk('pagination/PaginationActive', array('class' => 'active', 'text' => $i))
			: $discuss->getChunk('pagination/PaginationLink', array( 'url' => $currentResourceUrl.'&page='.$i, 'text' => $i));
	}

    
/* Truncate */
} else {
	switch($total){
		/* If the current page near the begginning */
		case $current <= 3:
			/* first page */
			$list[] = ($current == 1)
				? $discuss->getChunk('pagination/PaginationActive', array('class' => 'active', 'text' => 1))
				: $discuss->getChunk('pagination/PaginationLink',array('url' => $currentResourceUrl.'&page=', 'text' => 1));

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
		
			$list[] = $discuss->getChunk('pagination/PaginationLink', Array('url' => $currentResourceUrl.'&page=', 'text' => '1'));
			$list[] = $discuss->getChunk('pagination/PaginationLink', Array('url' => $currentResourceUrl.'&page='.(2), 'text' => '2'));
			$list[] = $truncateText;

			for ($i = $total - (2 + ($adj * 2)); $i <= $total; $i++) {
				$list[] = ($i == $current) ?
					$discuss->getChunk('pagination/PaginationActive', Array('class' => 'active', 'text' => $i)) :
					$discuss->getChunk('pagination/PaginationLink', Array('url' => $currentResourceUrl.'&page='.($i), 'text' => $i));
			}
		break;
	}
}

/* Next button */
if ($current == $total) {
	$list[] = $discuss->getChunk('pagination/PaginationActive',Array('class' => 'inactive', 'text' => '►'));
} else {
	$list[] = $discuss->getChunk('pagination/PaginationLink',Array('url' => $currentResourceUrl.'&page='.($current+1), 'text' => '►'));
}

$list = implode("\n",$list);
/* wrap the pagination */
$pagination = $discuss->getChunk('pagination/PaginationWrapper',Array('content' => $list));

/* Send it to the browser */
$modx->toPlaceholders(array('pagination' => $pagination));

return;