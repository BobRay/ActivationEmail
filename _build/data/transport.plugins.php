<?php
/**
 * ActivationEmail transport plugins
 * Copyright 2011-2022 Bob Ray <https://bobsguides.com>
 * @author Bob Ray <https://bobsguides.com>
 * 1/1/11
 *
 * ActivationEmail is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * ActivationEmail is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ActivationEmail; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package activationemail
 */
/**
 * Description:  Array of plugin objects for ActivationEmail package
 * @package activationemail
 * @subpackage build
 */

if (! function_exists('getPluginContent')) {
    function getpluginContent($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<?php','',$o);
        $o = str_replace('?>','',$o);
        $o = trim($o);
        return $o;
    }
}
$plugins = array();
/** @var $modx modX */
$plugins[1]= $modx->newObject('modplugin');
$plugins[1]->fromArray(array(
    'id' => 1,
    'name' => 'ActivationEmail',
    'description' => 'Sends email to use on manual activation or (optionally) deactivation.',
    'plugincode' => getPluginContent($sources['source_core'].'/elements/plugins/activationemail.plugin.php'),
),'',true,true);
$properties = include $sources['data'].'/properties/properties.activationemail.php';
$plugins[1]->setProperties($properties);
unset($properties);



return $plugins;