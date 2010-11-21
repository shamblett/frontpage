<?php
/**
 * Settings data script
 *
 * @category  Editing
 * @package   Front Page
 * @author    S. Hamblett <steve.hamblett@linux.com>
 * @copyright 2010 S. Hamblett
 * @license   GPLv3 http://www.gnu.org/licenses/gpl.html
 * @link      none
 **/

				
/* System Settings */

$datasetting = $modx->newObject('modSystemSetting');
$datasetting->fromArray(array(
				'key' => 'edit_resource',
				'value' => 0,
				'xtype' => 'textfield',
				'namespace' => 'frontpage',
				'area' => 'Frontpage'
				), '', true, true);
$settings[] = $datasetting;
unset($datasetting);

$datasetting = $modx->newObject('modSystemSetting');
$datasetting->fromArray(array(
				'key' => 'create_resource',
				'value' => "0",
				'xtype' => 'textfield',
				'namespace' => 'frontpage',
				'area' => 'Frontpage'
				), '', true, true);
$settings[] = $datasetting;
unset($datasetting);

$datasetting = $modx->newObject('modSystemSetting');
$datasetting->fromArray(array(
				'key' => 'default_template',
				'value' => "parent",
				'xtype' => 'textfield',
				'namespace' => 'frontpage',
				'area' => 'Frontpage'
				), '', true, true);

$settings[] = $datasetting;


