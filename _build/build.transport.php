<?php

/**
 * @package frontpage
 * @subpackage build
 */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

$base = dirname(dirname(__FILE__)) . '/';
$sources = array(
    'root' => $base . '/',
    'assets' => 'assets/components/frontpage',
    'core' => 'core/components/frontpage',
    'docs' => $base . '/assets/components/frontpage/docs/',
    'chunks' => 'chunks/',
    'snippets' => 'snippets/',
    'templates' => 'templates/',
    'plugins' => 'plugins/',
    'events' => 'plugins/events/',
    'properties' => 'properties/',
    'resolvers' => 'resolvers/',
    'settings' => 'settings/',
    'resources' => 'resources/',
    'source_core' => $base . '/core/components/frontpage',
    'source_assets' => $base . '/assets/components/frontpage',
    'lexicon' => $base . 'core/components/frontpage/lexicon/',
    'model' => $base . 'core/components/frontpage/model/',
);
unset($base);

require_once dirname(__FILE__) . '/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('mgr');
echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$name = 'frontpage';
$version = '1.3.0';
$release = 'beta';

$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage($name, $version, $release);
$builder->registerNamespace('frontpage', false, true, '{core_path}components/frontpage/');

$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
        'modChunk' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name'),
        'modTemplate' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'templatename'),
        'modPlugin' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name'),
        xPDOTransport::RELATED_OBJECTS => true,
        xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
            'modPluginEvent' => array(
                xPDOTransport::PRESERVE_KEYS => true,
                xPDOTransport::UPDATE_OBJECT => false,
                xPDOTransport::UNIQUE_KEY => array('pluginid', 'event'),
        )),
        'modSnippet' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name')
    )
);

$category = $modx->newObject('modCategory');
$category->set('category', 'Frontpage');

/* Get plugin */
include_once($sources['plugins'] . 'plugins.php');

/* Get chunks */
include_once($sources['chunks'] . 'chunks.php');

/* Get snippets */
include_once($sources['snippets'] . 'snippets.php');

/* Get template */
include_once($sources['templates'] . 'template.php');

/* Add category items */
$category->addMany($plugins);
$category->addMany($chunks);
$category->addMany($snippets);
$category->addMany($templates);

/* create a transport vehicle for the category data object */
$vehicle = $builder->createVehicle($category, $attr);
$vehicles[] = $vehicle;

/* Settings */
require_once $sources['settings'] . 'settings.data.php';

$attr = array(
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => 'key');

foreach ($settings as $setting) {

    $vehicle = $builder->createVehicle($setting, $attr);
    $vehicles[] = $vehicle;
}

/* Resources */
$attr = array(
    xPDOTransport::UNIQUE_KEY => 'pagetitle',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true);

include_once($sources['resources'] . 'resources.php');

foreach ($resources as $resource) {
    $vehicle = $builder->createVehicle($resource, $attr);
    $vehicles[] = $vehicle;
}

/* Resolvers, both php and file on the last vehicle */
$vehicle = end($vehicles);

$vehicle->resolve('php', array(
    'type' => 'php',
    'source' => $sources['resolvers'] . 'resolver.php'));
$vehicle->resolve('file', array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';"));
$vehicle->resolve('file', array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';"));


/* Add all the vehicles */
foreach ($vehicles as $vehicle) {
    $builder->putVehicle($vehicle);
}

/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
));

/* zip up the package */
$builder->pack();

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tend = $mtime;
$totalTime = ($tend - $tstart);
$totalTime = sprintf("%2.4f s", $totalTime);

$modx->log(MODX_LOG_LEVEL_INFO, "<br />\nPackage Built.<br />\nExecution time: {$totalTime}\n");

exit();
