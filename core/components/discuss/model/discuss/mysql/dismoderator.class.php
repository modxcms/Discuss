<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/dismoderator.class.php');
class disModerator_mysql extends disModerator {}
?>