<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/dislogactivity.class.php');
class disLogActivity_mysql extends disLogActivity {}
?>