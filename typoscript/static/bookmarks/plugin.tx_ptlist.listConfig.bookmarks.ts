plugin.tx_ptlist.listConfig.bookmarks {
	
	tables = tx_ptlist_bookmarks

	baseWhereClause (
		(tx_ptlist_bookmarks.feuser = "{TSFE:fe_user|user|uid}" OR tx_ptlist_bookmarks.feuser = "0")
		AND tx_ptlist_bookmarks.list = "{register:listId}"
	)
	baseWhereClause.insertData = 1
	
	data {
		10 {
			identifier = uid
			table = tx_ptlist_bookmarks
			field = uid
		}
		20 {
			identifier = name
			table = tx_ptlist_bookmarks
			field = name
		}
	}
	
	columns {
		10 {
			columnIdentifier = nameColumn
			label = Bookmarks

			dataDescriptionIdentifier = name, uid
			renderObj = TEXT
			renderObj {
				field = name
				typolink {
					parameter.data = page:uid
					additionalParams = &tx_ptlist_controller_list_listId_{register:listId}[bookmark_uid]={field:uid}
					additionalParams.insertData = 1
				}
				wrap = <img src="/typo3conf/ext/pt_list/res/bookmark_document.png" />|
			}
		}
	}
	
}