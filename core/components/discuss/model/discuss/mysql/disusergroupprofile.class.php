<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/disusergroupprofile.class.php');
class disUserGroupProfile_mysql extends disUserGroupProfile {}
?>