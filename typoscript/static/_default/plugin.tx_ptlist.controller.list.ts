plugin.tx_ptlist.controller.list {

	# itemListMode = extjs

	pagerStrategyClass < plugin.tx_ptlist.alias.pagerStrategy_default

	itemsPerPage = 15
	
	pagerStrategyConfiguration {
		delta = 3
		elements = first, prev, pages, next, last, offsetinfo
	}

}