<?php
/**
 * Frontpage Resources
 *
 * @package frontpage
 * @author S. Hamblett steve.hamblett@linux.com Nov 2010
 */

/* Resources */
$resources = array();

/* Container */
$r = $modx->newObject('modResource');
$r->set('class_key','modDocument');
$r->set('context_key','web');
$r->set('type','document');
$r->set('contentType','text/html');
$r->set('pagetitle','Frontpage');
$r->set('longtitle','Frontpage Editor Resources');
$r->set('alias','frontpage');
$r->set('published','1');
$r->set('parent','0');
$r->set('isfolder','1');
$r->set('richtext','0');
$r->set('menuindex','0');
$r->set('searchable','0');
$r->set('cacheable','0');
$r->set('donthit','0');
$r->set('menutitle', '');
$r->set('hidemenu','1');
$r->set('template',0);

$resources[] = $r;
unset($r);

/* Create */
$r = $modx->newObject('modResource');
$r->set('class_key','modDocument');
$r->set('context_key','web');
$r->set('type','document');
$r->set('contentType','text/html');
$r->set('pagetitle','Frontpage Create');
$r->set('longtitle','Frontpage Editor Create Resource');
$r->set('description','');
$r->set('alias','create');
$r->set('published','1');
$r->set('parent','0');
$r->set('isfolder','0');
$r->set('introtext', '');
$r->setContent(file_get_contents($sources['resources'] . 'create.html'));
$r->set('richtext','0');
$r->set('menuindex','0');
$r->set('searchable','0');
$r->set('cacheable','0');
$r->set('donthit','0');
$r->set('menutitle', '');
$r->set('hidemenu','1');
$r->set('template',0);

$resources[] = $r;
unset($r);

/* Edit */
$r = $modx->newObject('modResource');
$r->set('class_key','modDocument');
$r->set('context_key','web');
$r->set('type','document');
$r->set('contentType','text/html');
$r->set('pagetitle','Frontpage Edit');
$r->set('longtitle','Frontpage Editor Edit Resource');
$r->set('alias','edit');
$r->set('published','1');
$r->set('parent','0');
$r->set('isfolder','0');
$r->setContent(file_get_contents($sources['resources'] . 'edit.html'));
$r->set('richtext','0');
$r->set('menuindex','0');
$r->set('searchable','0');
$r->set('cacheable','0');
$r->set('donthit','0');
$r->set('menutitle', '');
$r->set('hidemenu','1');
$r->set('template',0);

$resources[] = $r;
unset($r);

/* AJAX */
$r = $modx->newObject('modResource');
$r->set('class_key','modDocument');
$r->set('context_key','web');
$r->set('type','document');
$r->set('contentType','text/plain');
$r->set('pagetitle','Frontpage AJAX');
$r->set('longtitle','Frontpage Editor AJAX Resource');
$r->set('alias','ajax');
$r->set('published','1');
$r->set('parent','0');
$r->set('isfolder','0');
$r->setContent(file_get_contents($sources['resources'] . 'ajax.html'));
$r->set('richtext','0');
$r->set('menuindex','0');
$r->set('searchable','0');
$r->set('cacheable','0');
$r->set('donthit','0');
$r->set('menutitle', '');
$r->set('hidemenu','1');
$r->set('template',0);

$resources[] = $r;
unset($r);