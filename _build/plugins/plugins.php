<?php
/**
 * Front Page Plugin
 *
 * @package Frontpage
 * @author S. Hamblett steve.hamblett@linux.com Nov 2010
 */ 

$plugins = array();
$p = $modx->newObject('modPlugin');
$p->set('name', 'Frontpage');
$p->set('description', 'Frontpage editor');
$p->set('plugincode', file_get_contents($sources['plugins'] . 'frontpage.txt'));
$properties = include $sources['properties'] . 'properties.frontpage.php';
$p->setProperties($properties);
unset($properties);

include $sources['events'].'events.frontpage.php';
$p->addMany($events);
unset($events);

$plugins[] = $p;
unset($p);




