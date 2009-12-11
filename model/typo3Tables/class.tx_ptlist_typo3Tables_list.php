<?php

require_once t3lib_extMgm::extPath('pt_list') . 'model/class.tx_ptlist_list.php';
require_once t3lib_extMgm::extPath('pt_list') . 'model/class.tx_ptlist_filter.php';
require_once t3lib_extMgm::extPath('pt_list') . 'model/class.tx_ptlist_dataDescription.php';
require_once t3lib_extMgm::extPath('pt_list') . 'model/class.tx_ptlist_dataDescriptionCollection.php';
require_once t3lib_extMgm::extPath('pt_list') . 'model/class.tx_ptlist_filterCollection.php';
require_once t3lib_extMgm::extPath('pt_list') . 'model/class.tx_ptlist_columnDescription.php';
require_once t3lib_extMgm::extPath('pt_list') . 'model/class.tx_ptlist_columnDescriptionCollection.php';
require_once t3lib_extMgm::extPath('pt_list') . 'model/typo3Tables/class.tx_ptlist_typo3Tables_dataObject.php';
require_once t3lib_extMgm::extPath('pt_list') . 'model/typo3Tables/class.tx_ptlist_typo3Tables_dataObjectCollection.php';

require_once t3lib_extMgm::extPath('pt_tools') . 'res/staticlib/class.tx_pttools_assert.php';
require_once t3lib_extMgm::extPath('pt_tools') . 'res/abstract/class.tx_pttools_iSettableByArray.php';


/**
 * TYPO3 tables class
 *
 * @version 	$Id$
 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
 * @since		2009-01-21
 * @package     TYPO3
 * @subpackage  pt_list\model\typo3Tables
 */
class tx_ptlist_typo3Tables_list extends tx_ptlist_list implements tx_pttools_iSettableByArray {

	/**
	 * @var string	base SQL WHERE clause from configuration
	 */
	protected $baseWhereClause;

    /**
     * @var string  base SQL GROUP BY clause from configuration
     */
	protected $baseGroupByClause;

    /**
     * @var string  base SQL FROM clause from configuration
     */
	protected $baseFromClause;

	/**
 	 * @var array	involved tables
	 */
	protected $tables = array();
	
	

	/***************************************************************************
	 * Implementing abstract methods from the "tx_ptlist_list" class
	 **************************************************************************/

	/**
	 * Setup list
	 *
	 * @param 	void
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	public function setup() {

		// read configuration array from typoscript in "plugin.tx_ptlist.listConfig.<listIdentifier>."
		$typoscriptPath = 'plugin.tx_ptlist.listConfig.'.$this->listId.'.';
		$this->conf = tx_pttools_div::getTS($typoscriptPath);
		tx_pttools_assert::isNotEmptyArray($this->conf, array('message' => sprintf('No typoscript configuration found under "%s"!', $typoscriptPath)));
		
		$this->setPropertiesFromArray($this->conf);
	}
	
	
	
	/***************************************************************************
	 * Methods for the "tx_ptlist_iSettableByArray" interface
	 **************************************************************************/
	
	/**
	 * Sets the properties from an array
	 *
	 * @param 	array 	data array
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-03-13
	 */
	public function setPropertiesFromArray(array $dataArray) {
		
		tx_pttools_assert::isNotEmptyArray($dataArray['data.'], array('message' => 'No data defined in typoscript configuration!'));
		tx_pttools_assert::isNotEmptyArray($dataArray['columns.'], array('message' => 'No columns defined in typoscript configuration!'));

		// setup a unique database accessor for this
		tx_ptlist_typo3Tables_dataObjectAccessor::getInstanceById($this->listId);

		// switch to another database if configured
		if (!empty($dataArray['database'])) {
			tx_ptlist_typo3Tables_dataObjectAccessor::getInstanceById($this->listId)->setDatabase($dataArray['database']);
		}

		// tables
		$this->tables = t3lib_div::trimExplode(',', $dataArray['tables']);
		tx_pttools_assert::isNotEmptyArray($this->tables, array('message' => 'No tables found in configuration!'));

		// SQL base where clause
		$this->baseWhereClause = $GLOBALS['TSFE']->cObj->stdWrap($dataArray['baseWhereClause'], $dataArray['baseWhereClause.']);
		if (empty($this->baseWhereClause)) {
			$this->baseWhereClause = '1=1';	
		}

		// SQL base group by clause
		$this->baseGroupByClause = $GLOBALS['TSFE']->cObj->stdWrap($dataArray['baseGroupByClause'], $dataArray['baseGroupByClause.']);

		// SQL base from clause
		$this->baseFromClause = $GLOBALS['TSFE']->cObj->stdWrap($dataArray['baseFromClause'], $dataArray['baseFromClause.']);
		
        // text do display if no elements have been found for a list request (added by rk 2009-08-28)  # TODO: Replace this by a translation mechanism
        $this->noElementsFoundText = $GLOBALS['TSFE']->cObj->stdWrap($dataArray['noElementsFoundText'], $dataArray['noElementsFoundText.']);

		// setup dataDescriptions
		$this->dataDescriptions = new tx_ptlist_dataDescriptionCollection();
		// the default table is the first defined table (or its alias)
		$this->dataDescriptions->set_defaultTable(end(t3lib_div::trimExplode(' ', $this->tables[0])));
		$this->dataDescriptions->setPropertiesFromArray($dataArray['data.']);

		// setup columns
		$this->columnDescriptions = new tx_ptlist_columnDescriptionCollection($this->listId);
		$this->columnDescriptions->setPropertiesFromArray($dataArray['columns.']);
		
		// hide columns
		$this->hideColumns = $GLOBALS['TSFE']->cObj->stdWrap($dataArray['hideColumns'], $dataArray['hideColumns.']);

		// setup filters
		if (is_array($dataArray['filters.'])) {
			$this->filters = new tx_ptlist_filterCollection($this->listId);
			$this->filters->setPropertiesFromArray($dataArray['filters.']);
		}

		if(!empty($dataArray['defaults.']['sortingColumn'])) {
			$sortingColumn = $GLOBALS['TSFE']->cObj->stdWrap($dataArray['defaults.']['sortingColumn'], $dataArray['defaults.']['sortingColumn.']);
			$sortingDirection = $GLOBALS['TSFE']->cObj->stdWrap($dataArray['defaults.']['sortingDirection'], $dataArray['defaults.']['sortingDirection.']);
			$sortingDirection = (strtolower($sortingDirection) == 'desc') ? tx_ptlist_columnDescription::SORTINGSTATE_DESC : tx_ptlist_columnDescription::SORTINGSTATE_ASC;
			$this->setSortingParameters($sortingColumn, $sortingDirection);
		}
		
	}


	/***************************************************************************
	 * Methods for the "tx_ptlist_iFilterable" interface
	 **************************************************************************/

	/**
	 * Get group data.
	 * This method allows filters to get some information about the current state of the list object
	 *
	 * @param 	string	select clause
	 * @param 	string	(optional) where clause
	 * @param 	string	(optional) group by clause
	 * @param 	string	(optional) order by clause
	 * @param 	string	(optional) limit clause
	 * @param 	string	(optional) csl of filter identifiers that should be ignored when retrivieving the (other) filter's where clauses, or "__ALL__" to ignore all filters
	 * @return 	array 	array of records
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-12
	 */
	public function getGroupData($select, $where='', $groupBy='', $orderBy='', $limit='', $ignoredFiltersForWhereClause='') {
		if ($ignoredFiltersForWhereClause != '__ALL__') {
			$whereClauseFromOtherFilters = $this->getAllFilters()->getSqlWhereClauseSnippet($ignoredFiltersForWhereClause);
		}

		$whereClause = $whereClauseFromOtherFilters;
		$whereClause .= (!empty($whereClauseFromOtherFilters) && !empty($where) ? ' AND ' : '');
		$whereClause .= $where;

		tx_pttools_assert::isNotEmptyString($this->listId, array('message' => 'No "listId" found!'));

		return tx_ptlist_typo3Tables_dataObjectAccessor::getInstanceById($this->listId)->getGroupData($this->listId, $select, $whereClause, $groupBy, $orderBy, $limit);
	}



	/***************************************************************************
	 * Methods for the "tx_pttools_iPageable" interface
	 **************************************************************************/

	/**
	 * Get total item count
	 *
	 * @param 	void
	 * @return 	int	total item count of items in the collection
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-22
	 */
	public function getTotalItemCount() {
		$where = $this->getAllFilters()->getSqlWhereClauseSnippet();
		return tx_ptlist_typo3Tables_dataObjectAccessor::getInstanceById($this->listId)->countRows($this->listId, $where);
	}



	/**
	 * Get collection "part" for given limit parameter
	 *
	 * @param 	string	mysql limit clause
	 * @return 	tx_ptlistdemo_country_countryCollection
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	public function getItems($limit = '') {

		$dataObjectCollection = new tx_ptlist_typo3Tables_dataObjectCollection($this->listId);

		$select = $this->getAllColumnDescriptions()->getSelectClause(); /* @var $select string */
		tx_pttools_assert::isNotEmptyString($select, array('message' => 'Select clause was empty!'));
		$where = $this->getAllFilters()->getSqlWhereClauseSnippet();
		$orderBy = $this->getAllColumnDescriptions()->getOrderByClause();
		$groupBy = '';

		$dataObjectCollection->loadItems($select, $where, $orderBy, $limit, $groupBy);

		return $dataObjectCollection;
	}


	
	/**
	 * Get single aggregate value
	 *
	 * @param 	string	aggregateDataDescriptionIdentifier
	 * @param 	string	limit
	 * @return 	string	aggregate
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-23
	 */
	public function getSingleAggregate($aggregateDataDescriptionIdentifier, $limit) {
		
		$limit = ''; // MySQL does not support limit on aggregated values
		
		tx_pttools_assert::isNotEmptyString($aggregateDataDescriptionIdentifier, array('message' => '"aggregateDataDescriptionIdentifier" was for empty!'));
		$aggregateColumn = $this->conf['aggregateData.'][$aggregateDataDescriptionIdentifier];
		tx_pttools_assert::isNotEmptyString($aggregateColumn, array('message' => sprintf('No config found for "%s"', $aggregateDataDescriptionIdentifier)));

		$fields = $aggregateColumn . ' AS agg';
		$where = $this->getAllFilters()->getSqlWhereClauseSnippet();
		$orderBy = ''; // $this->getAllColumnDescriptions()->getOrderByClause();
		$groupBy = $this->get_baseGroupByClause();
		$rows = tx_ptlist_typo3Tables_dataObjectAccessor::getInstanceById($this->listId)->selectRows($this->listId, $fields, $where, $orderBy, $limit, $groupBy);
		tx_pttools_assert::isNotEmptyArray($rows, array('message' => 'No aggregate returned'));
		if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Aggregated value for "%s"', $aggregateDataDescriptionIdentifier), 'pt_list', 0, $rows);
		return $rows[0]['agg'];
	}



	/**
	 * Get all aggregates
	 * 
	 * @param	string	limit value (from pager)
	 * @return	array	array(<aggregateDataDescriptionIdentifier> => <aggregateValue>)
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-20
	 */
	public function getAllAggregates($limit) {
		$aggregates = array();
		if (is_array($this->conf['aggregateRows.'])) {
			foreach ($this->conf['aggregateRows.'] as $aggregateRow) {
				foreach ($aggregateRow as $columnDescription) {
					foreach (t3lib_div::trimExplode(',', $columnDescription['aggregateDataDescriptionIdentifier'], 1) as $aggregateDataDescriptionIdentifier) {
						if (empty($aggregates[$aggregateDataDescriptionIdentifier])) {
							$aggregates[$aggregateDataDescriptionIdentifier] = $this->getSingleAggregate($aggregateDataDescriptionIdentifier, $limit);
						}
					}
				}
			}
		}
		return $aggregates;
	}


	
	/**
	 * Returns an array with information about available aggregate rows
	 *
	 * The structure of the result array is:
	 * <code>
	 * $result = array(
	 * 		'<rowKey>' => array(
	 * 			'<columnDescriptionIdentifier>' => <columnDescriptionConfigurationArray>
	 * 		)
	 * );
	 * </code>
	 * 
	 * @param 	void
	 * @return 	array
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-23
	 */
	public function getAggregateRowInfo() {
		$rows = array();
		if (is_array($this->conf['aggregateRows.'])) {
			foreach ($this->conf['aggregateRows.'] as $rowKey => $aggregateRow) {
				$rows[$rowKey] = array();
				foreach ($aggregateRow as $columnDescriptionIdentifier => $columnDescription) {
					tx_pttools_assert::isNotEmptyArray($columnDescription, array('message' => sprintf('No valid configuration found in key "%s"!', $columnDescriptionIdentifier)));
					$rows[$rowKey][substr($columnDescriptionIdentifier, 0, -1) /* removing the dot */] = $columnDescription;
				}
			}
		}
		return $rows;
	}



	/**
	 * Get from clause
	 *
	 * @param	void
	 * @return	string	from clause
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-01
	 */
	public function getFromClause() {
		if (empty($this->baseFromClause)) {
			return implode(', ', $this->get_tables());
		} else {
			return $this->baseFromClause;
		}
	}

	/***************************************************************************
	 * Getter / Setter methods
	 **************************************************************************/

	public function get_tables() {
		return $this->tables;
	}

	public function get_baseWhereClause() {
		return $this->baseWhereClause;
	}

	public function get_baseGroupByClause() {
		return $this->baseGroupByClause;
	}

	public function get_baseFromClause() {
		return $this->baseFromClause;
	}
    
}

?>