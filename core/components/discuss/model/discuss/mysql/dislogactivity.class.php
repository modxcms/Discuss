<?php
/**
 * @package discuss
 * @subpackage mysql
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/dislogactivity.class.php');
/**
 * @package discuss
 * @subpackage mysql
 */
class disLogActivity_mysql extends disLogActivity {}