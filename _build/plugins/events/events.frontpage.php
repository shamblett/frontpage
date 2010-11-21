<?php
/**
 * Events for Frontpage plugin
 * 
 * @package frontpage
 * @subpackage build
 */
$events = array();

$event = $modx->newObject('modPluginEvent');
$event->set('event', 'OnWebPagePrerender');
$event->set('priority', 0);
$event->set('propertyset', 0);

$events[] = $event;
unset($event);

return $events;
