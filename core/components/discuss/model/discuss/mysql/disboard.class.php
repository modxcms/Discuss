<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/disboard.class.php');
class disBoard_mysql extends disBoard {}
?>