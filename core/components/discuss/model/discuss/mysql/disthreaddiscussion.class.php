<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/disthreaddiscussion.class.php');
class disThreadDiscussion_mysql extends disThreadDiscussion {}
?>