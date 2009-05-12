################################################################################
# Demo 17
#
# Mondial database example
#
# @version	$Id: setup.txt,v 1.16 2009/03/18 14:42:54 ry44 Exp $
# @author	Fabrizio Branca <mail@fabrizio-branca.de>
# @since	2009-04-15
################################################################################

plugin.tx_ptlist.listConfig.demo17 {

	database = mysql://mondial:mondial@localhost/Mondial

	baseFromClause (
		country
		LEFT JOIN encompasses ON (country.Code = encompasses.Country)
	)

	baseWhereClause (
	)
	
	
	baseGroupByClause (
	)
	

	# hideColumns = nameColumn


	############################################################################
	# Setting up the data descriptions
	############################################################################
	data {
		name {
			field = Name
			table = country 
		}
		
		code {
			field = Code
			table = country 
		}
		
		capital {
			field = Capital
			table = country 
		}
		
		province {
			field = Province
			table = country 
		}	
		
		area {
			field = Area
			table = country 
		}
		
		population {
			field = Population
			table = country
		}
		
		continent {
			field = Continent
			table = encompasses
		}
	}
	
	aggregateData {	
		areaSum = round(sum(country.Area))	
		areaAvg = round(avg(country.Area))
		areaMax = round(max(country.Area))
		areaMin = round(min(country.Area))
		
		populationSum = round(sum(country.Population))	
		populationAvg = round(avg(country.Population))
		populationMax = round(max(country.Population))
		populationMin = round(min(country.Population))
	}
	
	aggregateRows.10 {
		populationColumn {
			aggregateDataDescriptionIdentifier = populationSum, populationAvg, populationMax, populationMin
			renderObj = TEXT
			renderObj.value (
				 Min.: <b>{field:populationMin}</b><br /> 
				 &empty;: <b>{field:populationAvg}</b><br />
				 Max.: <b>{field:populationMax}</b><br />
				 &sum;: <b>{field:populationSum}</b><br />
			)
			renderObj.insertData = 1
		}
		areaColumn {
			aggregateDataDescriptionIdentifier = areaSum, areaAvg, areaMax, areaMin
			renderObj = TEXT
			renderObj.value (
				 Min.: <b>{field:areaMin}</b><br /> 
				 &empty;: <b>{field:areaAvg}</b><br />
				 Max.: <b>{field:areaMax}</b><br />
				 &sum;: <b>{field:areaSum}</b><br />
			)
			renderObj.insertData = 1
		}
	}


	############################################################################
	# Display columns configuration
	############################################################################
	columns {
		10 {
			label = Country
			columnIdentifier = nameColumn
			dataDescriptionIdentifier = name
		}
		
		
		20 {
			label = Code
			columnIdentifier = codeColumn
			dataDescriptionIdentifier = code
		}
		
		30 {
			label = Capital
			columnIdentifier = capitalColumn
			dataDescriptionIdentifier = capital
		}
		
		40 {
			label = Province
			columnIdentifier = provinceColumn
			dataDescriptionIdentifier = province
		}	
		
		50 {
			label = LLL:EXT:pt_list/typoscript/static/demolist/locallang.xml:demo17_areaLabel
			columnIdentifier = areaColumn
			dataDescriptionIdentifier = area
		}
		
		60 {
			label = Population
			columnIdentifier = populationColumn
			dataDescriptionIdentifier = population
			/*
			renderObj = TEXT
			renderObj {
				cObject = TEXT
				cObject.value = {field:population}/1000
				cObject.insertData = 1
				prioriCalc = intval
			}
			*/
		}
		
		70 {
			label = Continent
			columnIdentifier = continentColumn
			dataDescriptionIdentifier = continent
		}
	}	
	
	filters.defaultFilterbox {
	
		10 < plugin.tx_ptlist.alias.filter_string
		10 {
			filterIdentifier = stringSearch
			label = Search all fields
			dataDescriptionIdentifier = *
		}
	
		20 < plugin.tx_ptlist.alias.filter_range
		20 {
			filterIdentifier = areaRange
			label = Area range filter
			dataDescriptionIdentifier = area
		}
	
		30 < plugin.tx_ptlist.alias.filter_range
		30 {
			filterIdentifier = poplationRange
			label = Population range filter
			dataDescriptionIdentifier = population
		}
	}
	
	filters.renderInList {

		10 < plugin.tx_ptlist.alias.filter_options_group
		10 {
			filterIdentifier = groupContinents
			label = Select continent
			dataDescriptionIdentifier = continent
			hideResetLink = 1
			mode = select
			submitOnChange = 1
			includeEmptyOption = 1
			includeEmptyOption.label = [All continents]
		}
	}
}

plugin.tx_ptlist_listConfig_demo17._CSS_DEFAULT_STYLE (
	.tx-ptlist-list-header form {
		margin: 0;
		padding: 0;
	}
)

plugin.tx_ptlist.view.list_itemList.template = EXT:pt_list/typoscript/static/demolist/demo17_list_itemList.tpl

page.1 = LOAD_REGISTER
page.1.listId = demo17