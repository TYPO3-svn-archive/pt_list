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
	class > 
	template = none
	template {
		row_stdWrap.wrap = <item>|</item>
		field_stdWrap.dataWrap = <{field:columnIdentifier}>|</{field:columnIdentifier}>
		list_stdWrap.wrap (
			<?xml version="1.0" encoding="utf-8"?>
	 			<rss version="2.0">			 
				  <channel>
					<title>pt_list, RSS demo</title>
					<link>http://www.google.de</link>
					<description>Kurze Beschreibung des Feeds</description>
					<language>de-de</language>
					<copyright>Autor des Feeds</copyright>
					<pubDate>Tue, 8 Jul 2008 2:43:19</pubDate>
					<image>
					  <url>URL einer einzubindenden Grafik</url>
					  <title>Bildtitel</title>
					  <link>URL, mit der das Bild verknuepft ist</link>
					</image>
					|
				</channel>
			</rss>
		)
	}
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
			dataDescriptionIdentifier = name_en
		}
		
		20 {
			columnIdentifier = description
			dataDescriptionIdentifier = *
			renderObj = TEXT
			renderObj.value = Insert description here
		}
		
		30 {
			columnIdentifier = link
			dataDescriptionIdentifier = *
			renderObj = TEXT
			renderObj.value = http://www.google.de
		}
		
		40 {
			columnIdentifier = guid
			dataDescriptionIdentifier = countryuid
		}
		
		50 {
			columnIdentifier = pubDate
			dataDescriptionIdentifier = *
			renderObj = TEXT
			renderObj {
				data = date : r
			}
		}
	}	
}