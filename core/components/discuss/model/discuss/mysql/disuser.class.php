<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/disuser.class.php');
class disUser_mysql extends disUser {}
?>