<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/dispostclosure.class.php');
class disPostClosure_mysql extends disPostClosure {}
?>