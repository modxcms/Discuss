<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/disboardclosure.class.php');
class disBoardClosure_mysql extends disBoardClosure {}
?>