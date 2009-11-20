<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/disbanitem.class.php');
class disBanItem_mysql extends disBanItem {}
?>