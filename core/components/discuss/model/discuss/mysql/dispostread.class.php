<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/dispostread.class.php');
class disPostRead_mysql extends disPostRead {}
?>