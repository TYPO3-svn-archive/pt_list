<?php

########################################################################
# Extension Manager/Repository config file for ext: "pt_list"
#
# Auto generated 11-03-2009 14:09
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Generic list handler',
	'description' => '',
	'category' => 'plugin',
	'author' => 'Fabrizio Branca, Rainer Kuhn',
	'author_email' => 't3extensions@punkt.de',
	'shy' => '',
	'dependencies' => 'pt_tools,pt_mvc,smarty',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'punkt.de GmbH',
	'version' => '0.0.1dev',
	'constraints' => array(
		'depends' => array(
			'pt_tools' => '0.4.1-',
			'pt_mvc' => '',
			'smarty' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		  'static_info_tables' => '',
          'pt_xml2pdf' => '',
		),
	),
	'_md5_values_when_last_written' => 'a:122:{s:9:"ChangeLog";s:4:"ee97";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"4546";s:17:"ext_localconf.php";s:4:"b729";s:14:"ext_tables.php";s:4:"f7ac";s:14:"ext_tables.sql";s:4:"9894";s:28:"icon_tx_ptlist_bookmarks.gif";s:4:"475a";s:28:"icon_tx_ptlist_databases.gif";s:4:"475a";s:13:"locallang.xml";s:4:"3215";s:16:"locallang_db.xml";s:4:"cfcb";s:14:"tca_ptlist.php";s:4:"b7c3";s:48:"classes/class.tx_ptlist_flexformDataProvider.php";s:4:"ca9d";s:14:"doc/DevDoc.txt";s:4:"6817";s:14:"doc/manual.sxw";s:4:"2ee7";s:19:"doc/wizard_form.dat";s:4:"664f";s:20:"doc/wizard_form.html";s:4:"2878";s:46:"controller/class.tx_ptlist_controller_list.php";s:4:"4bb1";s:39:"controller/flexform_controller_list.xml";s:4:"0da9";s:74:"controller/filter/class.tx_ptlist_controller_filter_controllerFrontend.php";s:4:"e49a";s:67:"controller/filter/class.tx_ptlist_controller_filter_firstLetter.php";s:4:"2974";s:59:"controller/filter/class.tx_ptlist_controller_filter_max.php";s:4:"ba53";s:59:"controller/filter/class.tx_ptlist_controller_filter_min.php";s:4:"e847";s:61:"controller/filter/class.tx_ptlist_controller_filter_range.php";s:4:"8fe8";s:62:"controller/filter/class.tx_ptlist_controller_filter_string.php";s:4:"27f0";s:64:"controller/filter/class.tx_ptlist_controller_filter_timeSpan.php";s:4:"2b1b";s:76:"controller/filter/options/class.tx_ptlist_controller_filter_options_base.php";s:4:"6203";s:80:"controller/filter/options/class.tx_ptlist_controller_filter_options_explicit.php";s:4:"29fa";s:77:"controller/filter/options/class.tx_ptlist_controller_filter_options_group.php";s:4:"c9c8";s:34:"model/class.tx_ptlist_bookmark.php";s:4:"cbfc";s:42:"model/class.tx_ptlist_bookmarkAccessor.php";s:4:"1555";s:44:"model/class.tx_ptlist_bookmarkCollection.php";s:4:"b57f";s:43:"model/class.tx_ptlist_columnDescription.php";s:4:"61bc";s:53:"model/class.tx_ptlist_columnDescriptionCollection.php";s:4:"4c44";s:41:"model/class.tx_ptlist_dataDescription.php";s:4:"8338";s:51:"model/class.tx_ptlist_dataDescriptionCollection.php";s:4:"b73e";s:29:"model/class.tx_ptlist_div.php";s:4:"d6be";s:51:"model/class.tx_ptlist_externalDatabaseConnector.php";s:4:"193e";s:32:"model/class.tx_ptlist_filter.php";s:4:"6e84";s:42:"model/class.tx_ptlist_filterCollection.php";s:4:"d008";s:45:"model/class.tx_ptlist_genericDataAccessor.php";s:4:"bebc";s:30:"model/class.tx_ptlist_list.php";s:4:"f68a";s:31:"model/class.tx_ptlist_pager.php";s:4:"325e";s:34:"model/class.tx_ptlist_renderer.php";s:4:"27ff";s:48:"model/interfaces/class.tx_ptlist_iFilterable.php";s:4:"63fd";s:46:"model/interfaces/class.tx_ptlist_iListable.php";s:4:"f5db";s:51:"model/interfaces/class.tx_ptlist_iPagerStrategy.php";s:4:"248f";s:61:"model/pagerStrategy/class.tx_ptlist_pagerStrategy_default.php";s:4:"86d9";s:60:"model/typo3Tables/class.tx_ptlist_typo3Tables_dataObject.php";s:4:"59ee";s:68:"model/typo3Tables/class.tx_ptlist_typo3Tables_dataObjectAccessor.php";s:4:"2a1b";s:70:"model/typo3Tables/class.tx_ptlist_typo3Tables_dataObjectCollection.php";s:4:"196e";s:54:"model/typo3Tables/class.tx_ptlist_typo3Tables_list.php";s:4:"4693";s:58:"model/typo3Tables/class.tx_ptlist_typo3Tables_renderer.php";s:4:"52fa";s:25:"res/bookmark_document.png";s:4:"7a43";s:21:"res/button_cancel.png";s:4:"c1d3";s:27:"res/icon_table_sort_asc.png";s:4:"598e";s:31:"res/icon_table_sort_default.png";s:4:"6d4b";s:28:"res/icon_table_sort_desc.png";s:4:"7b73";s:26:"res/library_bookmarked.png";s:4:"933f";s:20:"res/mi_arr2_down.gif";s:4:"4cc6";s:18:"res/mi_arr2_up.gif";s:4:"37aa";s:37:"template/filter/filter_breadcrumb.tpl";s:4:"06f7";s:64:"template/filter/firstLetter/filter_firstLetter_userInterface.tpl";s:4:"a364";s:48:"template/filter/max/filter_max_userInterface.tpl";s:4:"db73";s:48:"template/filter/min/filter_min_userInterface.tpl";s:4:"0d6c";s:52:"template/filter/range/filter_range_userInterface.tpl";s:4:"9ef2";s:54:"template/filter/string/filter_string_userInterface.tpl";s:4:"9208";s:68:"template/filter/stringGeneric/filter_stringGeneric_userInterface.tpl";s:4:"1c05";s:58:"template/filter/timeSpan/filter_timeSpan_userInterface.tpl";s:4:"59e2";s:79:"template/filter/options/userInterface/filter_options_userInterface_checkbox.tpl";s:4:"a37b";s:76:"template/filter/options/userInterface/filter_options_userInterface_links.tpl";s:4:"2fd2";s:76:"template/filter/options/userInterface/filter_options_userInterface_radio.tpl";s:4:"6877";s:77:"template/filter/options/userInterface/filter_options_userInterface_select.tpl";s:4:"6a29";s:32:"template/list/list_filterbox.tpl";s:4:"55fa";s:39:"template/list/list_filterbreadcrumb.tpl";s:4:"a9d6";s:31:"template/list/list_itemList.tpl";s:4:"bc5c";s:28:"template/list/list_pager.tpl";s:4:"254e";s:68:"template/list/bookmarks/list_bookmarks_displayAvailableBookmarks.tpl";s:4:"2494";s:47:"template/list/bookmarks/list_bookmarks_form.tpl";s:4:"2c33";s:53:"template/list/extjsList/list_extjsList_headerdata.tpl";s:4:"8a39";s:44:"template/list/itemList/list_itemList_pdf.tpl";s:4:"678e";s:40:"typoscript/static/demolist/locallang.xml";s:4:"c2da";s:80:"typoscript/static/demolist/plugin.tx_ptlist_myDemoList._CSS_DEFAULT_STYLE.ts.css";s:4:"de63";s:36:"typoscript/static/demolist/setup.txt";s:4:"678a";s:33:"typoscript/static/demolist/un.gif";s:4:"1598";s:36:"typoscript/static/_default/config.ts";s:4:"d076";s:33:"typoscript/static/_default/lib.ts";s:4:"cd21";s:38:"typoscript/static/_default/pageAjax.ts";s:4:"2b16";s:52:"typoscript/static/_default/plugin.tx_ptlist.alias.ts";s:4:"eb74";s:62:"typoscript/static/_default/plugin.tx_ptlist.controller.list.ts";s:4:"c935";s:46:"typoscript/static/_default/plugin.tx_ptlist.ts";s:4:"20ae";s:36:"typoscript/static/_default/setup.txt";s:4:"ea5c";s:34:"typoscript/static/_default/temp.ts";s:4:"0b66";s:42:"typoscript/static/_themes/_default/old.css";s:4:"957c";s:77:"typoscript/static/_themes/_default/plugin.tx_ptlist._CSS_DEFAULT_STYLE.ts.css";s:4:"e491";s:44:"typoscript/static/_themes/_default/setup.txt";s:4:"aa47";s:68:"typoscript/static/bookmarks/plugin.tx_ptlist.listConfig.bookmarks.ts";s:4:"f2f2";s:37:"typoscript/static/bookmarks/setup.txt";s:4:"262f";s:29:"view/class.tx_ptlist_view.php";s:4:"6262";s:54:"view/filter/class.tx_ptlist_view_filter_breadcrumb.php";s:4:"bec5";s:81:"view/filter/firstLetter/class.tx_ptlist_view_filter_firstLetter_userInterface.php";s:4:"0787";s:65:"view/filter/max/class.tx_ptlist_view_filter_max_userInterface.php";s:4:"9aa5";s:65:"view/filter/min/class.tx_ptlist_view_filter_min_userInterface.php";s:4:"89aa";s:69:"view/filter/range/class.tx_ptlist_view_filter_range_userInterface.php";s:4:"3292";s:71:"view/filter/string/class.tx_ptlist_view_filter_string_userInterface.php";s:4:"afc7";s:85:"view/filter/stringGeneric/class.tx_ptlist_view_filter_stringGeneric_userInterface.php";s:4:"e5db";s:75:"view/filter/timeSpan/class.tx_ptlist_view_filter_timeSpan_userInterface.php";s:4:"c95c";s:88:"view/filter/options/class.tx_ptlist_view_filter_options_userInterface_advmultiselect.php";s:4:"606b";s:82:"view/filter/options/class.tx_ptlist_view_filter_options_userInterface_checkbox.php";s:4:"4538";s:79:"view/filter/options/class.tx_ptlist_view_filter_options_userInterface_links.php";s:4:"c68c";s:79:"view/filter/options/class.tx_ptlist_view_filter_options_userInterface_radio.php";s:4:"9061";s:80:"view/filter/options/class.tx_ptlist_view_filter_options_userInterface_select.php";s:4:"39aa";s:75:"view/list/class.tx_ptlist_view_list_bookmarks_displayAvailableBookmarks.php";s:4:"c0d7";s:54:"view/list/class.tx_ptlist_view_list_bookmarks_form.php";s:4:"9ea9";s:60:"view/list/class.tx_ptlist_view_list_extjsList_headerdata.php";s:4:"765a";s:49:"view/list/class.tx_ptlist_view_list_filterbox.php";s:4:"f235";s:56:"view/list/class.tx_ptlist_view_list_filterbreadcrumb.php";s:4:"da93";s:48:"view/list/class.tx_ptlist_view_list_itemList.php";s:4:"5220";s:45:"view/list/class.tx_ptlist_view_list_pager.php";s:4:"1bae";s:61:"view/list/itemList/class.tx_ptlist_view_list_itemList_csv.php";s:4:"52c5";s:68:"view/list/itemList/class.tx_ptlist_view_list_itemList_imageGraph.php";s:4:"1b0a";s:61:"view/list/itemList/class.tx_ptlist_view_list_itemList_pdf.php";s:4:"4729";s:43:"tests/class.tx_ptlist_renderer_testcase.php";s:4:"581c";}',
	'suggests' => array(
	),
);

?>