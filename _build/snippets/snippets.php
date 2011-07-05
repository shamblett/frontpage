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

$s = $modx->newObject('modSnippet');
$s->set('name', 'frontpageAJAX');
$s->set('description', 'Frontpage AJAX resource control snippet');
$s->set('snippet', file_get_contents($sources['snippets'] . 'ajax.txt'));
$snippets[] = $s;

$s = $modx->newObject('modSnippet');
$s->set('name', 'frontpageAJAX-Aloha');
$s->set('description', 'Frontpage AJAX Aloha resource control snippet');
$s->set('snippet', file_get_contents($sources['snippets'] . 'ajax-aloha.txt'));
$snippets[] = $s;

$s = $modx->newObject('modSnippet');
$s->set('name', 'frontpageCreate-Aloha');
$s->set('description', 'Frontpage Aloha create resource control; snippet');
$s->set('snippet', file_get_contents($sources['snippets'] . 'create-aloha.txt'));
$snippets[] = $s;

$s = $modx->newObject('modSnippet');
$s->set('name', 'frontpageEdit-Aloha');
$s->set('description', 'Frontpage Aloha edit resource control; snippet');
$s->set('snippet', file_get_contents($sources['snippets'] . 'edit-aloha.txt'));
$snippets[] = $s;

