<?php
/**
 * ActivationEmail transport chunks
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
 * Description: Array of chunk objects for ActivationEmail package
 * @package activationemail
 * @subpackage build
 */

$chunks = array();

$chunks[1]= $modx->newObject('modChunk');
$chunks[1]->fromArray(array(
    'id' => 1,
    'name' => 'ActivationEmailTpl',
    'description' => 'Tpl chunk for email message to activated users',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/activationemail.chunk.tpl'),
    'properties' => '',
),'',true,true);

$chunks[2]= $modx->newObject('modChunk');
$chunks[2]->fromArray(array(
    'id' => 2,
    'name' => 'DeactivationEmailTpl',
    'description' => 'Tpl chunk for email message to activated users',
    'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/deactivationemail.chunk.tpl'),
    'properties' => '',
),'',true,true);

return $chunks;