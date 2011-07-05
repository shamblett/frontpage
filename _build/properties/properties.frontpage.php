<?php
/**
 * Frontpage Properties
 *
 * @package frontpage
 *
 * @author S. Hamblett steve.hamblett@linux.com Nov 2010
 */
$properties = array(
    array(
        'name' => 'loadJQuery',
        'desc' => 'Load jQuery from this package, set to false if you already have jQuery in your front end.',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
    ),
    array(
        'name' => 'jQueryNoConflict',
        'desc' => 'Set jQuery to no conflict mode.',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
    ),
    array(
        'name' => 'showCreate',
        'desc' => 'Show the create button',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => true,
    ),
    array(
        'name' => 'showEdit',
        'desc' => 'Show the edit button',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => true,
    ),
    array(
        'name' => 'autohideToolbar',
        'desc' => 'Autohide the toolbar.',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => true,
    ),
    array(
        'name' => 'performRoleCheck',
        'desc' => 'Check roles for access to the toolbar.',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => false,
    ),
    array(
        'name' => 'contentManagerRoles',
        'desc' => 'Roles allowed access to the toolbar(Super User always has access).',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    ),
    array(
        'name' => 'boxWidth',
        'desc' => 'Colorbox width.',
        'type' => 'textfield',
        'options' => '',
        'value' => '90%',
    ),
    array(
        'name' => 'boxHeight',
        'desc' => 'Colorbox height.',
        'type' => 'textfield',
        'options' => '',
        'value' => '90%',
    ),
    array(
        'name' => 'jQueryPath',
        'desc' => 'URL path to a jQuery script.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
    ),
    array(
        'name' => 'editMethod',
        'desc' => 'Frontpage edit/create method',
        'type' => 'textfield',
        'options' => '',
        'value' => 'classic',
    ),
    array(
        'name' => 'activeAloha',
        'desc' => 'Uas Aloha on marked up pages',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => 'false',
    ));


return $properties;
