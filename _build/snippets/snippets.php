<?php
/**
 * Fronpage Snippets
 *
 * @package Frontpage
 * @author S. Hamblett steve.hamblett@linux.com Nov 2010
 */ 

$snippets = array();
$s = $modx->newObject('modSnippet');
$s->set('name', 'frontpageEdit');
$s->set('description', 'Frontpage edit resource control; snippet');
$s->set('snippet', file_get_contents($sources['snippets'] . 'edit.txt'));
$snippets[] = $s;

$s = $modx->newObject('modSnippet');
$s->set('name', 'frontpageCreate');
$s->set('description', 'Frontpage create resource control snippet');
$s->set('snippet', file_get_contents($sources['snippets'] . 'create.txt'));
$snippets[] = $s;

