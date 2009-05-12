################################################################################
# Demo 01
#
# Very simple list with data from the "static_countries" table joined with the
# "static_territories" table.
#
# @version	$Id: setup.txt,v 1.16 2009/03/18 14:42:54 ry44 Exp $
# @author	Fabrizio Branca <mail@fabrizio-branca.de>
# @since	2009-04-15
################################################################################

plugin.tx_ptlist.listConfig.demo01 {

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

	# hideColumns = nameColumn


	############################################################################
	# Setting up the data descriptions
	############################################################################

	data {

		name_local {
			table = static_countries
			field = cn_short_local

			# default for isSortable is "1"
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
			# access = 2,1
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
			# choose a columnIdentifier
			columnIdentifier = nameColumn

			# This label will be displayed in the item list (use lll here)
			label = LLL:EXT:pt_list/typoscript/static/demolist/locallang.xml:column_nameColumn

			# Set the dataDescriptions this columns refers to
			# the dataDescriptionIdentifier is a single reference to a dataDescription if type is default
			# or a csl list of referencs if type is "virtual"
			dataDescriptionIdentifier = name_local, name_en, countryuid, uno_member

			# a column is sortable if "isSortable" is set to 1 _and_
			# all sortingColumns entries this column refers to are sortable too
			isSortable = 1

			# sortingDataDescription is a reference to one dataDescription that will be used for sorting
			# if not set, the first entry in dataDescriptionIdentifier will be used
			sortingDataDescription = name_local

			renderObj = COA
			renderObj {
				5 = IMAGE
				5.if {
					value.data = field:uno_member
					equals = 1
				}
				5.file = EXT:pt_list/typoscript/static/demolist/un.gif
				5.stdWrap.typolink.parameter = http://www.un.org
				5.stdWrap.typolink.ATagParams = class="un-link"

				10 = TEXT
				10.data = field:name_en
				10.append = TEXT
				10.append {
					data = field:name_local
					if {
						value.data = field:name_local
						equals.data = field:name_en
						negate = 1
					}
				}
				10.append.noTrimWrap = | (|)|
				10.wrap3 = |&nbsp;

				20 = TEXT
				20.value = Details
				20.typolink.parameter = 1
				20.typolink.additionalParams.dataWrap = &tx_unseretolleextension_controller_details[countryuid]={field:countryuid}
 			}

		}

		11 {
			label = Capital
			columnIdentifier = capital
			dataDescriptionIdentifier = capital
		}

		20 {
			label = LLL:EXT:pt_list/typoscript/static/demolist/locallang.xml:column_isoNoColumn
			columnIdentifier = isoNoColumn
			dataDescriptionIdentifier = iso2
			isSortable = 1
			renderUserFunctions {
				10 = EXT:pt_list/typoscript/static/demolist/class.tx_ptlist_demolist_renderer.php:tx_ptlist_demolist_renderer->iso2CodeRenderer
			}
		}
		30 {
			label = Phone
			columnIdentifier = phoneColumn
			# default columns can only operate on _one_ dataDescription
			dataDescriptionIdentifier = phone
		}

		40 {
			label = Continent
			columnIdentifier = continent
			dataDescriptionIdentifier = continent
		}

		50 {
			label = Subcontinent
			columnIdentifier = subcontinent
			dataDescriptionIdentifier = subcontinent
		}
	}
}

<INCLUDE_TYPOSCRIPT: source="FILE:EXT:pt_list/typoscript/static/demolist/plugin.tx_ptlist_myDemoList._CSS_DEFAULT_STYLE.ts.css">
