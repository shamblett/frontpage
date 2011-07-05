<?php
/**
 * Frontpage Chunks
 *
 * @package frontpage
 *
 * @author S. Hamblett steve.hamblett@linux.com Nov 2010
 */ 

$chunks = array();
$c= $modx->newObject('modChunk');
$c->set('name', 'frontpageEdit');
$c->set('description', 'Front Page edit resource chunk');
$c->set('snippet', file_get_contents($sources['chunks'] . 'edit.html'));
$chunks[] = $c;

$c= $modx->newObject('modChunk');
$c->set('name', 'frontpageCreate');
$c->set('description', 'Front Page create resource chunk');
$c->set('snippet', file_get_contents($sources['chunks'] . 'create.html'));
$chunks[] = $c;

$c= $modx->newObject('modChunk');
$c->set('name', 'frontpageEdit-Aloha');
$c->set('description', 'Front Page Aloha edit resource chunk');
$c->set('snippet', file_get_contents($sources['chunks'] . 'edit-aloha.html'));
$chunks[] = $c;

$c= $modx->newObject('modChunk');
$c->set('name', 'frontpageCreate-Aloha');
$c->set('description', 'Front Page Aloha create resource chunk');
$c->set('snippet', file_get_contents($sources['chunks'] . 'create-aloha.html'));
$chunks[] = $c;

$c= $modx->newObject('modChunk');
$c->set('name', 'jsAlohaTemplate');
$c->set('description', 'Front Page Aloha template include chunk');
$c->set('snippet', file_get_contents($sources['chunks'] . 'jsAlohaTemplate.html'));
$chunks[] = $c;

$c= $modx->newObject('modChunk');
$c->set('name', 'jsAlohaPage');
$c->set('description', 'Front Page Aloha page  include chunk');
$c->set('snippet', file_get_contents($sources['chunks'] . 'jsAlohaPage.html'));
$chunks[] = $c;