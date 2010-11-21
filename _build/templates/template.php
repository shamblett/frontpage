<?php
/**
 * @package frontpage
 * @subpackage build
 */

$templates = array();

$c= $modx->newObject('modTemplate');
$c->set('templatename', 'Frontpage');
$c->set('description', 'Frontpage editor template.');
$c->set('category', 0);
$c->set('content', file_get_contents($sources['templates'] . 'template.frontpage.html'));
$templates[] = $c;


