<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_ptlist_bookmarks"] = array (
    "ctrl" => array (
        'title'     => 'LLL:EXT:pt_list/locallang_db.xml:tx_ptlist_bookmarks',        
        'label'     => 'name',    
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => "ORDER BY crdate",    
        'delete' => 'deleted',    
        'enablecolumns' => array (        
            'disabled' => 'hidden',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca_ptlist.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ptlist_bookmarks.gif',
    ),
    "feInterface" => array (
        "fe_admin_fieldList" => "hidden, name, list, filterstates, feuser",
    )
);

$TCA["tx_ptlist_databases"] = array (
    "ctrl" => array (
        'title'     => 'LLL:EXT:pt_list/locallang_db.xml:tx_ptlist_databases',        
        'label'     => 'db',
		'label_alt' => 'username, host',
		'label_alt_force' => 1,   
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => "ORDER BY crdate",    
        'delete' => 'deleted',    
        'enablecolumns' => array (        
            'disabled' => 'hidden',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca_ptlist.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_ptlist_databases.gif',
    ),
    "feInterface" => array (
        "fe_admin_fieldList" => "hidden, host, db, username, pass",
    )
);




t3lib_div::loadTCA('tt_content');

/**
 * Parsing the array defined in the ext_localconf.php file
 */
foreach ($GLOBALS[$_EXTKEY.'_controllerArray'] as $prefix => $configuration) {
	
	// remove some unused fields
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.$prefix] = 'layout,select_key,pages,recursive';
	
	// Adds an entry to the list of plugins in content elements of type "Insert plugin"
	t3lib_extMgm::addPlugin(array('LLL:EXT:'.$_EXTKEY.'/locallang_db.xml:tt_content.list_type'.$prefix, $_EXTKEY.$prefix),'list_type');
	
	// Include flexform
	if ($configuration['includeFlexform']) {
		$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.$prefix] = 'pi_flexform';
		t3lib_extMgm::addPiFlexFormValue($_EXTKEY.$prefix, 'FILE:EXT:'.$_EXTKEY.'/controller/flexform'.$prefix.'.xml');
	}
}

include_once(t3lib_extMgm::extPath($_EXTKEY).'classes/class.tx_ptlist_flexformDataProvider.php');


/**
 * Include static templates.
 * Convention for the label: "[<extensionKey>] <templateName>"
 */
t3lib_extMgm::addStaticFile($_EXTKEY, 'typoscript/static/_default/', 			'[pt_list] List configuration');
t3lib_extMgm::addStaticFile($_EXTKEY, 'typoscript/static/_themes/_default/', 	'[pt_list] Themes / Default');
// add more themes here...
t3lib_extMgm::addStaticFile($_EXTKEY, 'typoscript/static/bookmarks/', 			'[pt_list] Bookmarks');
t3lib_extMgm::addStaticFile($_EXTKEY, 'typoscript/static/demolist/', 			'[pt_list] Demo List (static_info_tables)');

?>