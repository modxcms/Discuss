<?php
/**
 * @package discuss
 * @subpackage mysql
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/dissession.class.php');
/**
 * @package discuss
 * @subpackage mysql
 */
class disSession_mysql extends disSession {}