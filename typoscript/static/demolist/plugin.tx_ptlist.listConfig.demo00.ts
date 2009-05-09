################################################################################
# Demo 00
#
# Very simple list with data from the "static_countries" table joined with the
# "static_territories" table. Displayed columns directly correspond to data columns. 
#
# @version	$Id: setup.txt,v 1.16 2009/03/18 14:42:54 ry44 Exp $
# @author	Fabrizio Branca <mail@fabrizio-branca.de>
# @since	2009-04-15
################################################################################

plugin.tx_ptlist.listConfig.demo00 {

	############################################################################
	# General settings
	############################################################################
	# As the static_territories records point to other
	# records in the same table that describe the continent, the "static_territories"
	# table is joined twice
	tables (
		static_countries,
		static_territories st_continent,
		static_territories st_subcontinent
	)

	baseFromClause (
		static_countries
		LEFT JOIN static_territories AS	st_subcontinent	ON (static_countries.cn_parent_tr_iso_nr = st_subcontinent.tr_iso_nr)
		LEFT JOIN static_territories AS	st_continent ON (st_subcontinent.tr_parent_iso_nr = st_continent.tr_iso_nr)
	)

	baseWhereClause (
		st_continent.tr_name_en <> ''
		AND st_subcontinent.tr_name_en <> ''
	)

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


