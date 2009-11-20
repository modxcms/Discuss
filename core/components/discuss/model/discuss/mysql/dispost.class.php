<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/dispost.class.php');
class disPost_mysql extends disPost {}
?>