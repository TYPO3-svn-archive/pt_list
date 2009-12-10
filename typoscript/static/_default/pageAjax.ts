tt_content.stdWrap.dataWrap >

pageAjax = PAGE
pageAjax {
	typeNum = 117
	
	config {
		disableAllHeaderCode = 1
		admPanel = 0
	}
	
	10 = CONTENT
	10 {
		table = tt_content
		select {
			orderBy = sorting
			where = colPos=0
			languageField = sys_language_uid
		}
	}
}

ajaxSingleContent = PAGE
ajaxSingleContent {
	typeNum = 118
	
	config {
		disableAllHeaderCode = 1
		admPanel = 0
	}
	
	10 = RECORDS
	10 {
		source.data = GPvar:cid
		tables = tt_content
		dontCheckPid = 1
	}
}
