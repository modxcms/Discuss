<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/disbangroup.class.php');
class disBanGroup_mysql extends disBanGroup {}
?>