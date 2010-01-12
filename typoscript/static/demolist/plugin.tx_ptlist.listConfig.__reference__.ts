################################################################################
# Reference
#
# Very simple list with data from the "static_countries" table joined with the
# "static_territories" table. Displayed columns directly correspond to data columns. 
#
# @version	$Id: plugin.tx_ptlist.listConfig.demo00.ts 27552 2009-12-10 10:10:04Z fabriziobranca $
# @author	Fabrizio Branca <mail@fabrizio-branca.de>
# @since	2010-01-06
################################################################################



plugin.tx_ptlist.controller.list {
	itemsPerPage = 0
	maxRows = 10
	# bookmark_uid = 14
	doNotUseSession = 1
}

plugin.tx_ptlist.listConfig.<listIdentifier> {

	############################################################################
	# General settings
	############################################################################
	tables =
	baseFromClause =
	baseWhereClause =
	baseGroupByClause =
	database = <tx_ptlist_databases:uid> or mysql://<username>:<password>@<host>/<database>
	defaults {
		sortingColumn = <columnDescriptionIdentifier>
		sortingDirection = asc|desc
	}

	############################################################################
	# Setting up the data descriptions
	############################################################################
	data {

		name_local {
			table = static_countries
			field = cn_short_local
			isSortable = 1
		}

		name_en {
			field = cn_short_en
			table = static_countries
		}

		uno_member {
			field = cn_uno_member
			table = static_countries
		}

		capital {
			table = static_countries
			field = cn_capital
		}

		iso2 {
			table = static_countries
			field = cn_iso_2
			isSortable = 0
		}

		phone {
			table = static_countries
			field = cn_phone
		}

		isoNo {
			table = static_countries
			field = cn_currency_iso_nr
		}

		continent {
			table = st_continent
			field = tr_name_en
		}

		subcontinent {
			table = st_subcontinent
			field = tr_name_en
		}

		countryuid {
			table = static_countries
			field = uid
		}
	}


	############################################################################
	# Display columns configuration
	############################################################################
	columns {
	
		10 {
			label = Name
			columnIdentifier = nameColumn
			dataDescriptionIdentifier = name_en
			isSortable =
			access =
			sortingDataDescription =
			renderObj =
			renderUserFunctions {
				10 =
			}
		}
		
		20 {
			label = Capital
			columnIdentifier = capital
			dataDescriptionIdentifier = capital
		}

		30 {
			label = LLL:EXT:pt_list/typoscript/static/demolist/locallang.xml:column_isoNoColumn
			columnIdentifier = isoNoColumn
			dataDescriptionIdentifier = iso2
		}
		
		40 {
			label = Phone
			columnIdentifier = phoneColumn
			dataDescriptionIdentifier = phone
		}
		
		50 {
			label = Continent
			columnIdentifier = continent
			dataDescriptionIdentifier = continent
		}
		
		60 {
			label = Subcontinent
			columnIdentifier = subcontinent
			dataDescriptionIdentifier = subcontinent
		}
		
	}
}


