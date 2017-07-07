<?php

/**
 * ActivationEmail resolver script - runs on install.
 *
 * Copyright 2011 Bob Ray <https://bobsguides.com>
 * @author Bob Ray <https://bobsguides.com>
 * 2/1/11
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
 * Description: Resolver script for ActivationeEail package
 * @package activationemail
 * @subpackage build
 */

/* Example Resolver script */
$hasPlugins = true;
$hasTemplates = true;
$category = 'ActivationEmail';

$success = true;
$object->xpdo->log(xPDO::LOG_LEVEL_INFO,'Running PHP Resolver.');
switch($options[xPDOTransport::PACKAGE_ACTION]) {
    /* This code will execute during an install */
    case xPDOTransport::ACTION_INSTALL:
        /* Assign plugins to System events */
        if ($hasPlugins) {
            $pluginObj = $object->xpdo->getObject('modPlugin',array('name'=>'ActivationEmail'));
            $events[0] = 'OnUserBeforeSave';
            if (! $pluginObj) $object->xpdo->log(xPDO::LOG_LEVEL_INFO,'cannot get object: MyPlugin1');
            if (empty($events)) $object->xpdo->log(xPDO::LOG_LEVEL_INFO,'Cannot get System Events');
            if (!empty($events) && $pluginObj) {
                $object->xpdo->log(xPDO::LOG_LEVEL_INFO,'Assigning Events to Plugins');

                foreach($events as $event => $eventName) {
                    $intersect = $object->xpdo->newObject('modPluginEvent');
                    $intersect->set('event',$eventName);
                    $intersect->set('pluginid',$pluginObj->get('id'));
                    $intersect->save();
                }
            }
        }
        break;

    /* This code will execute during an upgrade */
    case xPDOTransport::ACTION_UPGRADE:

        /* put any upgrade tasks (if any) here such as removing
           obsolete files, settings, elements, resources, etc.
        */

        $success = true;
        break;

    /* This code will execute during an uninstall */
    case xPDOTransport::ACTION_UNINSTALL:
        $object->xpdo->log(xPDO::LOG_LEVEL_INFO,'Uninstalling . . .');
        $success = true;
        break;

}
$object->xpdo->log(xPDO::LOG_LEVEL_INFO,'Script resolver actions completed');
return $success;