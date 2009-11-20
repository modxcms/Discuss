<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/discategory.class.php');
class disCategory_mysql extends disCategory {}
?>