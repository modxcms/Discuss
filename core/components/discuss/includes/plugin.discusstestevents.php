<?php

$response = array();
switch ($modx->event->name) {
    case 'OnDiscussRenderHome':
        break;
    case 'OnDiscussRenderBoard':
        break;
    case 'OnDiscussRenderThread':
        break;
}
$modx->event->_output = $response;
return;