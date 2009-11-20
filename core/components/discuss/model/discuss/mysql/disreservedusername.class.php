<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/disreservedusername.class.php');
class disReservedUsername_mysql extends disReservedUsername {}
?>