################################################################################
# Demo 12
#
# tt_news example
#
# @version	$Id$
# @author	Fabrizio Branca <mail@fabrizio-branca.de>
# @since	2009-04-15
################################################################################

plugin.tx_ptlist.listConfig.demo12 {
	tables = tt_news
	data {
		datetime.field = datetime
		title.field = title
		image.field = image
		bodytext.field = bodytext
	}
	columns {
		10 {
			columnIdentifier = newsItem
			dataDescriptionIdentifier = *
			renderObj = COA
			renderObj {
				5 = TEXT
				5 {
					field = datetime
					strftime = %e. %B %Y
				}
				5.wrap = <p class="lighttext">|</p>
			
			
				10 = TEXT
				10.field = title
				10.wrap = <h3>|</h3>
				
				20 = IMAGE
				20 {
					file {
						import = uploads/pics/
						import {
							field = image
							listNum = 0
						}
						width = 470c
						height = 239c
					}
				}
				
				30 = TEXT
				30 {
					field = bodytext
					parseFunc =< lib.parseFunc_RTE
				}
				30.wrap = <p>|</p>
			}
		}
	}
	filters.defaultFilterbox {
	    10 < plugin.tx_ptlist.alias.filter_timeSpan
		10 {
			filterIdentifier = timespan
			label = 
			dataDescriptionIdentifier = datetime
			spans = today,thisweek,thismonth,thisyear,yesterday,lastweek,lastmonth,lastyear
		}
	}
}
plugin.tx_ptlist.view.list_itemList {
	template = none
	template {
		list_stdWrap.wrap = <div class="newslist">|</div>
		field_stdWrap.wrap = <div class="newsitem">|</div>
	}
}
plugin.tx_ptlist_listConfig_demo12._CSS_DEFAULT_STYLE (
	.newsitem {
		margin-bottom: 10px;
		border-bottom: 1px solid #ccc;
	}
	.newsitem h3 {
		margin: 0 0 5px 0;
	}
	.newsitem .lighttext {
		margin: 0;
	}
)