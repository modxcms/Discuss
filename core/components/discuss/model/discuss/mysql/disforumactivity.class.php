<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/disforumactivity.class.php');
class disForumActivity_mysql extends disForumActivity {}
?>