<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/dispostattachment.class.php');
class disPostAttachment_mysql extends disPostAttachment {}
?>