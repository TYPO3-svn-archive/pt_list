plugin.tx_ptlist.controller.list {

	# itemListMode = extjs

	pagerStrategyClass < plugin.tx_ptlist.alias.pagerStrategy_default

	itemsPerPage = 15
	
	pagerStrategyConfiguration {
		delta = 3
		elements = first, prev, pages, next, last, offsetinfo
	}
	
	# Following values will usually be set in the plugin's flexfom
	
	# pluginMode = list|filterbox|pager|bookmarks|filterbreadcrumb|bookmarkform|extjsList
	
	# listId =
	
	# listClass = EXT:pt_list/model/typo3Tables/class.tx_ptlist_typo3Tables_list.php:tx_ptlist_typo3Tables_list
	
	# filterboxId =
	
}