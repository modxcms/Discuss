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
 * Build breadcrumbs from data
 *
 * @var modX $modx
 * @var Discuss $discuss
 * @var array $scriptProperties
 * 
 * @package discuss
 * @subpackage hooks
 */
$tpl = $modx->getOption('breadcrumbLinkTpl',$scriptProperties,'breadcrumbs/disBreadcrumbsLink');
$activeTpl = $modx->getOption('breadcrumbActiveTpl',$scriptProperties,'breadcrumbs/disBreadcrumbsActive');
$containerTpl = $modx->getOption('breadcrumbsTpl',$scriptProperties,'breadcrumbs/disBreadcrumbs');
$firstCls = $modx->getOption('firstCls',$scriptProperties,'first');
$lastCls = $modx->getOption('firstCls',$scriptProperties,'last clearfix');
$altCls = $modx->getOption('altCls',$scriptProperties,'alt');
$separator = $modx->getOption('separator',$scriptProperties,"\n");

$output = array();
$idx = 0;
$total = count($scriptProperties['items']);
$alt = false;

foreach ($scriptProperties['items'] as $item) {
    $cls = $idx == 0 ? $firstCls : ($idx == ($total-1) ? $lastCls : '');
    if ($alt) { $cls .= ' '.$altCls; }
    $item['cls'] = $cls;
    $output[] = $discuss->getChunk(!empty($item['active']) ? $activeTpl : $tpl,$item);
    $idx++;
    $alt = !$alt;
}
$output = implode($separator,$output);
return $discuss->getChunk($containerTpl,array('items' => $output));