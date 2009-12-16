<?php
/**
 * Build the setup options form.
 *
 * @package discuss
 * @subpackage build
 */
/* set some default values */
$forumTitle = 'My Forums';
$demoDataChecked = '';
$useCss = ' checked="checked"';
$loadJQuery = '';

/* get values based on mode */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        $demoDataChecked = ' checked="checked"';
        break;
    case xPDOTransport::ACTION_UPGRADE:
    case xPDOTransport::ACTION_UNINSTALL:
        /* forum title */
        $setting = $modx->getObject('modSystemSetting',array(
            'key' => 'discuss.forum_title',
        ));
        if ($setting) $forumTitle = $setting->get('value');

        /* use css */
        $setting = $modx->getObject('modSystemSetting',array(
            'key' => 'discuss.use_css',
        ));
        if ($setting && !$setting->get('value')) $useCss = '';

        /* load jquery from discuss */
        $setting = $modx->getObject('modSystemSetting',array(
            'key' => 'discuss.load_jquery',
        ));
        if ($setting && $setting->get('value')) $loadJQuery = ' checked="checked"';
        break;
}

$c = $modx->newQuery('modTemplate');
$c->sortby('templatename','ASC');
$templates = $modx->getCollection('modTemplate',$c);
$templatesTpl = '';
foreach ($templates as $template) {
    $templatesTpl .= '<option value="'.$template->get('id').'">'.$template->get('templatename').'</option>';
}

/* do output html */
$output = '
<h2>Discuss Installer</h2>
<p>Thanks for installing Discuss! Please review the setup options below before proceeding.</p>
<br />

<h3>Default Resources</h3>
<label for="discuss-install_resources">Auto-Install Default Resources:</label>
<input type="checkbox" name="install_resources" id="discuss-install_resources" value="1" />
<p>Checking this will automatically install all the default Resources needed for Discuss. Dont check it if you already have the Resources placed.</p>
<br />

<label for="discuss-template">Template</label>
<select name="template" id="discuss-template">
'.$templatesTpl.'
</select>
<p>This is the Template that the Resources will be assigned to.</p>
<br />

<label for="discuss-forums_alias">Forums Resource Alias</label>
<input type="text" name="forums_alias" id="discuss-forums_alias" value="forums" />
<p>This will be the alias for the newly-created Forums resource.</p>
<br />

<h3>Demo Data</h3>

<label for="discuss-install_demodata">Install Demo Data:</label>
<input type="checkbox" name="install_demodata" id="discuss-install_demodata" value="1"'.$demoDataChecked.' />
<p>Checking this will install demo data. It is recommended to do this on your first install of Discuss.</p>
<br /><br />

<h3>Forum Options</h3>

<label for="discuss-forum_title">Forums Resource Alias</label>
<input type="text" name="forum_title" id="discuss-forum_title" value="'.$forumTitle.'" />
<p>The title of your forums.</p>
<br />

<label for="discuss-use_css">Use Default CSS</label>
<input type="checkbox" name="use_css" id="discuss-use_css" value="1"'.$use_css.' />
<p>Whether or not to use the default provided CSS.</p>
<br />

<label for="discuss-load_jquery">Discuss Loads jQuery</label>
<input type="checkbox" name="load_jquery" id="discuss-load_jquery" value="1"'.$use_css.' />
<p>Whether or not to have Discuss load jQuery (leave off if you are doing so in the Template).</p>
<br />
';


return $output;