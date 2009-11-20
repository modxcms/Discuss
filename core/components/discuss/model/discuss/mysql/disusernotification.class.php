<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/disusernotification.class.php');
class disUserNotification_mysql extends disUserNotification {}
?>