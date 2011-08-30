<?php

/**
 * Frontpage Validator
 *
 * @package frontpage
 * @author S. Hamblett steve.hamblett@linux.com Nov 2010
 */
$success = false;
$modx = & $object->xpdo;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {


    case xPDOTransport::ACTION_INSTALL;
        xPDOTransport::ACTION_UPGRADE;
        
        /* Do nothing for now */
        $success = true;
        break;
    
    case xPDOTransport::ACTION_UNINSTALL:
        
        /* Remove the associated event */
        
        /* Get the plugin id */
        $plugin = $modx->getObject('modPlugin', array('name' => 'Frontpage'));
        if (!$plugin) {
            $modx->log(xPDO::LOG_LEVEL_ERROR, 'Cant get plugin on uninstall');
            $success = false;
            break;
        }
        $pluginId = $plugin->get('id');
        
        /* Remove the event */
        $removed = $modx->removeObject('modPluginEvent', array('pluginid' => $pluginId,
                                                               'event' =>'OnWebPagePrerender'));
        if ( !$removed ) {
            
            $modx->log(xPDO::LOG_LEVEL_ERROR, 'Cant remove event on uninstall');
            $success = false;
            break;
        }
        
        $success = true;
        break;
    
}

return $success;