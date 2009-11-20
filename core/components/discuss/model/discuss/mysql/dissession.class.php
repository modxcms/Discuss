<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/dissession.class.php');
class disSession_mysql extends disSession {}
?>