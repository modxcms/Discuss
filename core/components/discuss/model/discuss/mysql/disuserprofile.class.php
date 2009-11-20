<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/disuserprofile.class.php');
class disUserProfile_mysql extends disUserProfile {}
?>