<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/disboardusergroup.class.php');
class disBoardUserGroup_mysql extends disBoardUserGroup {}
?>