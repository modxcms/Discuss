<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/disthreadquestion.class.php');
class disThreadQuestion_mysql extends disThreadQuestion {}
?>