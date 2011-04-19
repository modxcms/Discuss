<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/disthreaduser.class.php');
class disThreadUser_mysql extends disThreadUser {}
?>