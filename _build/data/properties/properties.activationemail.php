<?php

/**
 * Default properties for the ActivaionEmail plugin
 * @author Bob Ray <http://bobsguides.com>
 * 1/1/11
 *
 * @package activationemail
 * @subpackage build
 */


$properties = array(
    array(
        'name' => 'sendOnActivation',
        'desc' => 'ae_sendOnActivation_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => true,
        'lexicon' => 'activationemail:properties',
    ),
    array(
        'name' => 'sendOnDeactivation',
        'desc' => 'ae_sendOnDeactivation_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
        'lexicon' => 'activationemail:properties',
    ),
    array(
        'name' => 'activationEmailTpl',
        'desc' => 'ae_activationEmailTpl_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'ActivationEmailTpl',
        'lexicon' => 'activationemail:properties',
    ),
    array(
        'name' => 'deactivationEmailTpl',
        'desc' => 'ae_deactivationEmailTpl_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'DeactivationEmailTpl',
        'lexicon' => 'activationemail:properties',
    ),
    array(
        'name' => 'activationURL',
        'desc' => 'ae_activationURL_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'activationemail:properties',
    ),
    array(
        'name' => 'deactivationURL',
        'desc' => 'ae_deactivationURL_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'activationemail:properties',
    ),
    array(
        'name' => 'sitename',
        'desc' => 'ae_sitename_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'activationemail:properties',
    ),
    array(
        'name' => 'useFullname',
        'desc' => 'ae_useFullname_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
        'lexicon' => 'activationemail:properties',
    ),
);

return $properties;