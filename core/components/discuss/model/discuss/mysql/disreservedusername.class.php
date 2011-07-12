<?php
/**
 * @package discuss
 * @subpackage mysql
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/disreservedusername.class.php');
/**
 * @package discuss
 * @subpackage mysql
 */
class disReservedUsername_mysql extends disReservedUsername {}