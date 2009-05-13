page >
page = PAGE
page {
	config {
		disableAllHeaderCode = 1
		admPanel = 0
	}
	10 < styles.content.get
}


plugin.tx_ptlist.view.list_itemList {
	class = EXT:pt_list/view/list/itemList/class.tx_ptlist_view_list_itemList_csv.php:tx_ptlist_view_list_itemList_csv
}

plugin.tx_ptlist.view.csv_rendering {

	# Set whether file should be generated for I (send to browser), D (send to browser, force download)
	fileHandlingType = I
	
	# Set file name of download file
	fileName = export.csv
	
	# Set to 1 if date and timestamp should be used for filename
	useDateAndTimestampInFilename = 1
	
	# File prefix for default naming (with date and time in filename) (only works, if useDateAndTimestampInFilename is set to 1!)
	fileNamePrefix = csv_
	
}


plugin.tx_ptlist.controller.list {
	itemsPerPage = 0
	maxRows = 10
	# bookmark_uid = 14
	doNotUseSession = 1
}

plugin.tx_ptlist.listConfig.demo06 {

	columns >
	columns {
		
		10 {
			columnIdentifier = title
			label = Name
			dataDescriptionIdentifier = name_en
		}
		
		20 {
			columnIdentifier = capital
			label = Capital
			dataDescriptionIdentifier = capital
			
		}
		
		30 {
			columnIdentifier = phone
			label = Phone
			dataDescriptionIdentifier = phone
			
		}
		
	}	
}