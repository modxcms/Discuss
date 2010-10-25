<?php
/**
 * Build a nice pagination
 * @package discuss
 * @subpackage hooks
 */
function addParam($action, $param)
{
	if($param['page'] == 1){
		unset($param['page']);
	}
	$queryString = http_build_query($param, '', '&amp;');
	if(empty($queryString)){
		return '';
	}	
	return '?'.$queryString;
}
 
$param = $scriptProperties['param'];
$current = (!empty($_GET[$param]) && is_numeric($_GET[$param])) ? $_GET[$param] : 1 ;
$limit = $scriptProperties['limit'];
$total = $scriptProperties['total'];
$view = $scriptProperties['view'];
$viewId = $scriptProperties['id'];
$landing = 'discuss.'.$view.'_resource';
$action = $modx->makeUrl($modx->getOption($landing));

if(empty($action)){
	return '';
}

if($total <= 1){
	$pagination = '<div class="inactive">Page 1 of 1</div>';
	$modx->toPlaceholders(array('pagination' => $pagination));
	return;
}

/* Declare variable */
$prev = $current - 1; // Previous page number
$urlPrev = addParam($action, array($view => $viewId, $param => $prev));
$next = $current + 1; // Next page number
$urlNext = addParam($action, array($view => $viewId, $param => $next)); 
$n2l = $total - 1; // Next to last page number	
$urlN2l = addParam($action, array($view => $viewId, $param => $total - 1)); 
$urlLast = addParam($action,array($view => $viewId, $param => $total));
$truncateText = ' ... ';
$pagination = '';
$list = '';	
		
/* Previous button */
switch($current){
	case ($current == 2):
		$list .= $discuss->getChunk('PaginationLink',Array('url' => $urlPrev, 'text' => '◄'));
	break;
	case ($current > 2):
		$list .= $discuss->getChunk('PaginationLink',Array('url' => $urlPrev, 'text' => '◄'));
	break;
	default:
		$list .= $discuss->getChunk('PaginationActive',Array('class' => 'inactive', 'text' => '◄'));
	break;
}

/* If total pages under 12, don't truncate */
if($total < 12){
	$list .= ($current == 1) 
		? 	$discuss->getChunk('PaginationActive', Array('class' => 'active', 'text' => '1')) 
		: 	$discuss->getChunk('PaginationLink',Array('url' => addParam($action, array($view => $viewId)), 'text' => '1'));
	for ($i = 2; $i<=$total; $i++)
	{
		$list .= ($i == $current) 
			? $discuss->getChunk('PaginationActive', Array('class' => 'active', 'text' => $i)) 
			: $discuss->getChunk('PaginationLink', Array( 'url' => addParam($action, array($view => $viewId, $param => $i)), 'text' => $i));
	}
/* Truncate */
}else{
	switch($total){
		/* If the current page near the begginning */
		case ($current < 2 + ($adj * 2)):
			/* first page */
			$list .= ($current == 1) 
				? $discuss->getChunk('PaginationActive', Array('class' => 'active', 'text' => '1')) 
				: $discuss->getChunk('PaginationLink',Array('url' => addParam($action, array($view => $viewId)), 'text' => '1'));

			/* And the followings */
			for ($i = 2; $i < 4 + ($adj * 2); $i++)
			{
				$list .= ($i == $current) 
				? $discuss->getChunk('PaginationActive', Array('class' => 'active', 'text' => $i)) 
				: $discuss->getChunk('PaginationLink', Array('url' => addParam($action, array($view => $viewId, $param => $i)), 'text' => $i));
			}
			/* truncate */
			$list .= $truncateText;

			/* and the remaining pages */
			$list .= $discuss->getChunk('PaginationLink', Array('url' => $urlN2l, 'text' => $n2l));
			$list .= $discuss->getChunk('PaginationLink', Array('url' => $urlCount, 'text' => $total));
		break;
		/* If current page is in the middle */
		case ( (($adj * 2) + 1 < $current) && ($current < $total - ($adj * 2)) ):
			
			$list .= $discuss->getChunk('PaginationLink', Array('url' => addParam($action, array($view => $viewId)), 'text' => '1'));
			$list .= $discuss->getChunk('PaginationLink', Array('url' => addParam($action, array($view => $viewId, $param => 2)), 'text' => '2'));

			$list .= $truncateText;

			for ($i = $current - $adj; $i <= $current + $adj; $i++)
			{
				$list .= ($i == $current) ? 
					$discuss->getChunk('PaginationActive', Array('class' => 'active', 'text' => $i)) : 
					$discuss->getChunk('PaginationLink', Array('url' => addParam($action, array($view => $viewId, $param => $i)), 'text' => $i));
			}

			$pagination .= $truncateText;

			$list .= $discuss->getChunk('PaginationLink', Array('url' => $urlN2l, 'text' => $n2l));
			$list .= $discuss->getChunk('PaginationLink', Array('url' => $urlCount, 'text' => $total));
		break;
		/* If current page is near the end */
		default:
		
			$list .= $discuss->getChunk('PaginationLink', Array('url' => addParam($action, array($view => $viewId)), 'text' => '1'));
			$list .= $discuss->getChunk('PaginationLink', Array('url' => addParam($action, array($view => $viewId, $param => 2)), 'text' => '2'));

			$list .= $truncateText;

			for ($i = $total - (2 + ($adj * 2)); $i <= $total; $i++)
			{
				$list .= ($i == $current) ? 
					$discuss->getChunk('PaginationActive', Array('class' => 'active', 'text' => $i)) : 
					$discuss->getChunk('PaginationLink', Array('url' => addParam($action, array($view => $viewId, $param => $i)), 'text' => $i));
			}
		break;
	}
}

/* Next button */
if ($current == $total)
	$list .= $discuss->getChunk('PaginationActive',Array('class' => 'inactive', 'text' => '►'));
else
	$list .= $discuss->getChunk('PaginationLink',Array('url' => $urlNext, 'text' => '►'));

/* wrap the pagination */
$pagination = $discuss->getChunk('PaginationWrapper',Array('content' => $list));

/* Send it to the browser */
$modx->toPlaceholders(array('pagination' => $pagination));

return;