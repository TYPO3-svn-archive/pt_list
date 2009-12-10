<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$GLOBALS[$_EXTKEY.'_controllerArray'] = array(
	'_controller_list' => array(
		'includeFlexform' => true,
		'pluginIcon' => 'EXT:pt_list/res/list.png'
	), 
);

$cN = t3lib_extMgm::getCN($_EXTKEY);
foreach (array_keys($GLOBALS[$_EXTKEY.'_controllerArray']) as $prefix) {
	
	$path = t3lib_div::trimExplode('_', $prefix, 1);
	$path = implode('/', array_slice($path, 0, -1)); // remove class name from the end
	
	// Add PlugIn to Static Template #43
	t3lib_extMgm::addPItoST43($_EXTKEY, $path.'/class.'.$cN.$prefix.'.php', $prefix, 'list_type', 0);
}



$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][t3lib_extMgm::extPath('pt_list').'classes/class.tx_ptlist_flexformDataProvider.php']['availableListClasses'][] = array(
	'name' => 'TYPO3 Tables', 
	'path' => 'EXT:pt_list/model/typo3Tables/class.tx_ptlist_typo3Tables_list.php:tx_ptlist_typo3Tables_list',
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][t3lib_extMgm::extPath('pt_list').'classes/class.tx_ptlist_flexformDataProvider.php']['availablePagerStrategyClasses'][] = array(
	'name' => 'Default Pager',
	'path' => 'EXT:pt_list/model/pagerStrategy/class.tx_ptlist_pagerStrategy_default.php:tx_ptlist_pagerStrategy_default',
);

$GLOBALS['TYPO3_CONF_VARS']['FE']['debug'] = '0';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'][] = 'EXT:pt_list/model/class.tx_ptlist_div.php:tx_ptlist_div->hookEofe';

?>