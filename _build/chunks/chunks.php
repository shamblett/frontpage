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

