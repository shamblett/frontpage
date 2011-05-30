<?php
  /**
 * Frontpage Resolver
 *
 * @package frontpage
 * @author S. Hamblett steve.hamblett@linux.com Nov 2010
 */

$success = false;
$modx =& $object->xpdo;

switch($options[xPDOTransport::PACKAGE_ACTION]) {


    case xPDOTransport::ACTION_INSTALL;
			xPDOTransport::ACTION_UPGRADE;
					
            /* Resolve the plugin to its event */
            $modx->log(xPDO::LOG_LEVEL_INFO,'Attempting to attach plugin to event OnWebPagePrerender');
            
            /* Get the plugin id */
            $plugin = $modx->getObject('modPlugin', array('name' => 'Frontpage'));
            if ( !$plugin ) {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'Cant get plugin ');
				$success = false;
				break;
			}
			$pluginId = $plugin->get('id');
								
			/* Create and connect the event */
		    $pluginEvent= $modx->newObject('modPluginEvent');
			$pluginEvent->set('pluginid', $pluginId);
			$pluginEvent->set('event', 'OnWebPagePrerender');
			$pluginEvent->set('priority', 0);
			$pluginEvent->set('propertyset', 0);
			$success= $pluginEvent->save();
			if ( $success === false ) {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'Cannot link plugin to event');
				$success = false;
				break;
			}  
				
            /* Set the system settings from the created resource id's */
             $modx->log(xPDO::LOG_LEVEL_INFO,'Attempting to set system settings');

            $editSetting = $modx->getObject('modSystemSetting',
                                             array('key' => 'edit_resource',
                                                   'namespace' => 'frontpage'));
            if ( !$editSetting ) {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'Cant get edit resource system setting');
				$success = false;
				break;
			}

            $createSetting = $modx->getObject('modSystemSetting',
                                              array('key' => 'create_resource',
                                                    'namespace' => 'frontpage'));
            if ( !$createSetting ) {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'Cant get create resource system setting');
				$success = false;
				break;
			}
            
            $ajaxSetting = $modx->getObject('modSystemSetting',
                                             array('key' => 'ajax_resource',
                                                   'namespace' => 'frontpage'));
            if ( !$ajaxSetting ) {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'Cant get ajax resource system setting');
				$success = false;
				break;
			}

            /* Get the create, edit and AJAX resource id's */
            $editResource = $modx->getObject('modResource',
                                             array('pagetitle' => 'Frontpage Edit'));

            if ( !$editResource ) {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'Cant get frontpage edit resource');
				$success = false;
				break;
			}
            $editId = $editResource->get('id');

            $createResource = $modx->getObject('modResource',
                                               array('pagetitle' => 'Frontpage Create'));

            if ( !$createResource ) {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'Cant get frontpage create resource');
				$success = false;
				break;
			}
            $createId = $createResource->get('id');
            
            $ajaxResource = $modx->getObject('modResource',
                                               array('pagetitle' => 'Frontpage AJAX'));

            if ( !$ajaxResource ) {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'Cant get frontpage ajax resource');
				$success = false;
				break;
			}
            $ajaxId = $ajaxResource->get('id');

            /* Link them up */
            $editSetting->set('value', $editId);
            $success = $editSetting->save();
            if ( $success === false ) {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'Cannot save edit resource setting value');
				break;
			}

            $createSetting->set('value', $createId);
            $success = $createSetting->save();
            if ( $success === false ) {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'Cannot save create resource setting value');
				break;
			}
            
            $ajaxSetting->set('value', $ajaxId);
            $success = $ajaxSetting->save();
            if ( $success === false ) {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'Cannot save ajax resource setting value');
				break;
			}
            
            /* Link the page create/edit resources to the parent container */
            $modx->log(xPDO::LOG_LEVEL_INFO,'Linking resources to parent');

            $parentResource = $modx->getObject('modResource',
                                             array('pagetitle' => 'Frontpage'));

            if ( !$parentResource ) {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'Cant get frontpage parent resource');
				$success = false;
				break;
			}
            $parentId = $parentResource->get('id');

            $createResource->set('parent', $parentId);
            $editResource->set('parent', $parentId);
            $ajaxResource->set('parent', $parentId);

            /* Link the pages to the template */
			$template = $modx->getObject('modTemplate',
                                         array('templatename' => 'Frontpage') );
			if ( !$template ) {
				$modx->log(xPDO::LOG_LEVEL_ERROR,"Failed to get Frontpage template");
				$success = false;
				break;
			}

            $templateId = $template->get('id');
            $createResource->set('template', $templateId);
            $editResource->set('template', $templateId);
            $ajaxResource->set('template', 0);

            /* Save the resources */
            $success = $createResource->save();
            if ( $success === false ) {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'Cannot save create resource parent value');
				break;
			}
            
            $success = $editResource->save();
            if ( $success === false ) {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'Cannot save edit resource parent value');
				break;
			}
			
			$success = $ajaxResource->save();
            if ( $success === false ) {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'Cannot save ajax resource parent value');
				break;
			}
           
            /* Ok, all is well */
            $success = true;
			break;

        
        case xPDOTransport::ACTION_UNINSTALL:
            /* Do nothing for now */
            $success = true;
            break;

}
return $success;
?>
