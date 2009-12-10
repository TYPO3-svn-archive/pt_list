################################################################################
# Demo 10
#
# Amazon Example
#
# @version	$Id$
# @author	Fabrizio Branca <mail@fabrizio-branca.de>
# @since	2009-04-15
################################################################################

plugin.tx_ptlist.listConfig.demo10 {

	############################################################################
	# General settings
	############################################################################
	# As the static_territories records point to other
	# records in the same table that describe the continent, the "static_territories"
	# table is joined twice
	tables (
		tx_tsblog_posts
	)

	baseWhereClause (
		image <> ''
	)

	# hideColumns = nameColumn


	############################################################################
	# Setting up the data descriptions
	############################################################################
	data {
		image.field = image
	}


	############################################################################
	# Display columns configuration
	############################################################################
	columns {
		10 {
			columnIdentifier = imageColumn
			dataDescriptionIdentifier = image
			
			renderObj = IMAGE
			renderObj {

				file {
					import = uploads/tx_tsblog/
					import {
						field = image
						listNum = 0
					}
					width = 150c
					height = 150c
				}
			}
		}
	}	
}

plugin.tx_ptlist.controller.list.tx_ptlist_controller_list_listId_demo10 {
	itemsPerPage = 6
	pagerStrategyConfiguration {
		delta = 3
		elements = prev, next
	}
}

plugin.tx_ptlist_listConfig_demo10._CSS_DEFAULT_STYLE (
	.float-container-first ul.tx-ptlist-pager li.next,
	.float-container-last ul.tx-ptlist-pager li.prev {
		display: none;
	}
	.float-container-first ul.tx-ptlist-pager li.prev,
	.float-container-last ul.tx-ptlist-pager li.next {
		height: 154px;
	}
	.itemList-field {
		margin: 0px 2px;
		padding: 2px;
		border: 1px solid #ccc;
	}
	
	ul.tx-ptlist-pager li.prev, 
	ul.tx-ptlist-pager li.next {
		border: none;
	}
	
	ul.tx-ptlist-pager li.prev span.wrapper, 
	ul.tx-ptlist-pager li.next span.wrapper {
		display: none;
	}
	
	ul.tx-ptlist-pager li.prev {
		background: url(/typo3conf/ext/pt_list/typoscript/static/demolist/left.png) no-repeat center;
	}
	
	ul.tx-ptlist-pager li.next {
		background: url(/typo3conf/ext/pt_list/typoscript/static/demolist/right.png) no-repeat center;
	}
)

plugin.tx_ptlist.view.list_itemList {
	template = none
	template {
		list_stdWrap.wrap = <div class="itemlist">|</div>
		field_stdWrap.wrap = <div class="itemList-field" style="float: left">|</div>
	}
}


