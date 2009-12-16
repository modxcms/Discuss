<?php
/**
 * Build the setup options form.
 *
 * @package discuss
 * @subpackage build
 */
/* set some default values */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        break;
    case xPDOTransport::ACTION_UPGRADE:
    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

$output = '<label for="quip-install_resources">Auto-Install Default Resources:</label>
<input type="checkbox" name="install_resources" id="quip-install_resources" value="1" />
<p>Checking this will automatically install all the default Resources needed for Discuss. Dont check it if you already have the Resources placed.</p>
<br /><br />';

return $output;