<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Fabrizio Branca (mail@fabrizio-branca.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


require_once t3lib_extMgm::extPath('pt_list').'model/interfaces/class.tx_ptlist_iListable.php';
require_once t3lib_extMgm::extPath('pt_list').'model/interfaces/class.tx_ptlist_iFilterable.php';

require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_columnDescriptionCollection.php';
require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_dataDescriptionCollection.php';

require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iPageable.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iTemplateable.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_assert.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_sessionStorageAdapter.php';


/**
 * Base class for the a list object
 *
 * @version 	$Id$
 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
 * @since		2009-01-26
 */
abstract class tx_ptlist_list implements tx_ptlist_iListable, tx_ptlist_iFilterable, tx_pttools_iPageable {

	/**
	 * @var string
	 */
	protected $listId;

	/**
	 * @var tx_ptlist_dataDescriptionCollection
	 */
	protected $dataDescriptions;

	/**
	 * @var tx_ptlist_dataDescriptionCollection
	 */
	protected $aggregateDataDescriptions;

	/**
	 * @var tx_ptlist_columnDescriptionCollection
	 */
	protected $columnDescriptions;

	/**
	 * @var tx_ptlist_filterCollection
	 */
	protected $filters;
	
	/**
	 * @var string 	csl of columnDescriptionIdentifiers of the columns that should be hidden
	 */
	protected $hideColumns;
	
	/**
	 * @var array configuration array. Should be populated within the inheriting class
	 */
	protected $conf = array();



	/***************************************************************************
	 * Abstract methods
	 **************************************************************************/

	/**
	 * Setup list (e.g. from configuration)
	 * Be sure to set the columns and the filter property here
	 *
	 * @param 	void
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	abstract protected function setup();


	/***************************************************************************
	 * Methods implementing the domain logic
	 **************************************************************************/


	/**
	 * Update itself by processing all subcontrollers
	 *
	 * @param 	void
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-26
	 */
	public function update() {
		
		$this->getAllFilters()->processSubControllers();

		/**
		 * TODO: think of a better solution to this! Maybe calling a "prepareAction" where configuration is read for the getWhereClause...
		 *  
		 * The dontProcessTwice parameter comes from the configuration set in the inheriting class.
		 * It should be set to avoid fitler to be processed twice. This brings speed up and e.g. the
		 * "toggle" functionality in the options filters only work with this option set.
		 * The drawback is, that filters that have dependencies to each other won't work correctly then
		 * 
		 * This is something that should be solved by design!
		 */
		if (!$this->conf['dontProcessTwice']) {
			// process a second time to assure that filters will influence each other where necessary
			$this->getAllFilters()->processSubControllers();
		}
	}



	/**
	 * Set sorting parameters
	 * TODO: should this method be defined in the "tx_ptlist_iListable" interface?
	 * Or should we create a "tx_ptlist_iSortable" interface and include $this->getSortableColumns() there too?
	 *
	 * @param 	string	sorting field
	 * @param 	string	sorting direction
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	public function setSortingParameters($sortingField, $sortingDirection) {
		tx_pttools_assert::isNotEmptyString($sortingField, array('message' => 'Invalid sortingField parameter'));
		tx_pttools_assert::isInArray(
			$sortingDirection,
			array(tx_ptlist_columnDescription::SORTINGSTATE_ASC, tx_ptlist_columnDescription::SORTINGSTATE_DESC, tx_ptlist_columnDescription::SORTINGSTATE_NONE),
			array('message' => 'Invalid sortingDirection parameter')
		);

		foreach ($this->getAllColumnDescriptions() as $column) { /* @var $column tx_ptlist_columnDescription */
			if ($column->get_columnIdentifier() == $sortingField) {
				$column->set_sortingState($sortingDirection);
			} else {
				if ($column->isSortable()) {
					$column->set_sortingState(tx_ptlist_columnDescription::SORTINGSTATE_NONE);
				}
			}
		}
	}
	
	
	
	/**
	 * Reset sorting parameters
	 * 
	 * @param  void
	 * @return void
	 * @author Michael Knoll <knoll@punkt.de>
	 * @since  2009-04-28
	 */
	public function resetSortingParameters() {

		foreach ($this->getAllColumnDescriptions() as $column) { /* @var $column tx_ptlist_columnDescription */
			if ($column->isSortable()) {
				
				// Reset sorting state from column, if there is a default sorting state, reset default state
				if (isset($this->conf['defaults.']['sortingColumn']) && $column->get_columnIdentifier() == $this->conf['defaults.']['sortingColumn']) {
					if ($this->conf['defaults.']['sortingDirection'] == 'ASC') {
				        $column->set_sortingState(tx_ptlist_columnDescription::SORTINGSTATE_ASC);
					} elseif ($this->conf['defaults.']['sortingDirection'] == 'DESC') {
						$column->set_sortingState(tx_ptlist_columnDescription::SORTINGSTATE_DESC);
					} else {
						$column->set_sortingState(tx_ptlist_columnDescription::SORTINGSTATE_NONE);
					}
				} else {
				    $column->set_sortingState(tx_ptlist_columnDescription::SORTINGSTATE_NONE);
				}
        
			}
			
		}
		
		// Delete sorting states from session
        tx_pttools_sessionStorageAdapter::getInstance()->delete($GLOBALS['TSFE']->fe_user->user['uid'] . '_' . $this->listId . '_sortingColumn');
        tx_pttools_sessionStorageAdapter::getInstance()->delete($GLOBALS['TSFE']->fe_user->user['uid'] . '_' . $this->listId . '_sortingDirection');
        
	}



    /**
     * Invokes a filter collection to this list object.
     * E.g. when restoring filter states from session
     *
     * @param   tx_ptlist_filterCollection  filterCollection
     * @return  void
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-01-19
     */
    public function invokeFilterCollection(tx_ptlist_filterCollection $filterCollection) {
        if (TYPO3_DLOG) t3lib_div::devLog('Invoking a filterCollection', 'pt_list');

        foreach ($filterCollection as $filter) { /* @var $filter tx_ptlist_filter */
        	if (!$this->getAllFilters()->hasItem($filter->get_filterIdentifier())) {
        		throw new tx_pttools_exception(sprintf('Could not find filter "%s" the list\'s filter collection!', $filter->get_filterIdentifier()));
        	}
            $currentFilter = $this->getAllFilters()->getItemById($filter->get_filterIdentifier());
            tx_pttools_assert::isInstanceOf($currentFilter, 'tx_ptlist_filter', array('message' => 'Matching filter not found!'));
            $currentFilter->invokeFilter($filter);
        }
    }



	/***************************************************************************
	 * Methods implementing the "tx_ptlist_iListable" interface
	 **************************************************************************/

	/**
	 * Get a collection of sortable columns
	 *
	 * @param 	void
	 * @return 	tx_ptlist_columnDescriptionCollection
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-14
	 */
	public function getSortableColumns() {
		return $this->getAllColumnDescriptions()->getSortableColumns();
	}



	/**
	 * Get listId.
	 * In this case this is only a wrapper method for $this->get_listId() to make implement the tx_ptlist_iListable interface
	 *
	 * @param 	void
	 * @return 	string 	list identifier
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-26
	 */
	public function getListId() {
		return $this->get_listId();
	}



	/**
	 * Get a collection of all column descriptions
	 *
	 * @param 	bool	(optional) return only accessible columns for the currently logged in fe_users
	 * @param 	void
	 * @return 	tx_ptlist_columnDescriptionCollection
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-14
	 */
	public function getAllColumnDescriptions($onlyAccessible=true) {
		tx_pttools_assert::isInstanceOf($this->columnDescriptions, 'tx_ptlist_columnDescriptionCollection');
		if ($onlyAccessible == true) {
			// TODO: abstract from fe_users!
			return $this->columnDescriptions->getAccessibleColumns($GLOBALS['TSFE']->gr_list);
		} else {
			return $this->columnDescriptions;
		}
	}



	/**
	 * Get a collection of all columns
	 *
	 * TODO: define this method in the "tx_ptlist_iListable" interface?
	 *
	 * @param 	void
	 * @return 	tx_ptlist_dataDescriptionCollection
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-14
	 */
	public function getAllDataDescriptions() {

		tx_pttools_assert::isInstanceOf($this->dataDescriptions, 'tx_ptlist_dataDescriptionCollection');

		return $this->dataDescriptions;
	}
	


	/***************************************************************************
	 * Methods for the tx_ptlist_iFilterable interface
	 **************************************************************************/

	/**
	 * Get all filters
	 *
	 * @param 	bool	(optional) return only accessible filters for the currently logged in fe_users
	 * @param 	string	(optional) if not null the methods returns only filters for a given filterbox
	 * @param 	bool	(optional) remove dependent filters
	 * @return 	tx_ptlist_filterCollection
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-15
	 */
	public function getAllFilters($onlyAccessible=true, $filterboxId=NULL, $removeDependentFilters=false) {

		$filterCollection = $this->filters;

		// return empty collection if no filters are available
		if (empty($filterCollection)) {
			return new tx_ptlist_filterCollection($this->listId);
		}

		if ($onlyAccessible == true) {
			// TODO: abstract from fe_users!
			$filterCollection = $filterCollection->getAccessibleFilters($GLOBALS['TSFE']->gr_list);
		}

		if (!is_null($filterboxId)) {
			$filterCollection = $filterCollection->getFiltersForFilterbox($filterboxId);
		}

		// remove those filters that depend on the active state of another filter
		if ($removeDependentFilters) {
			foreach ($filterCollection as $filterIdentifier => $filter) { /* @var $filter tx_ptlist_filter */
				$dependsOn = $filter->get_dependsOn();
				if (!empty($dependsOn)) {
					if ($this->filters->getItemById($dependsOn)->get_isActive() == false) {
						$filterCollection->deleteItem($filterIdentifier);
					}
				}
			}
		}

		return $filterCollection;
	}



	/***************************************************************************
	 * Getter / Setter methods
	 **************************************************************************/

	/**
	 * Sets the list id
	 *
	 * @param 	string	list identifier
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-26
	 */
	public function set_listId($listId) {
		tx_pttools_assert::isString($listId, array('message' => 'List identifier must be string!'));
		$this->listId = $listId;
	}



	/**
	 * Gets the list id
	 *
	 * @param 	void
	 * @return 	string 	list identifier
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-26
	 */
	public function get_listId() {
		return $this->listId;
	}
	

	
	/**
	 * Get hide columns
	 *
	 * @param 	void
	 * @return 	string 	csl of columnDescriptionIdentifiers of columns that should be hidden
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-03-13
	 */
	public function get_hideColumns() {
		return $this->hideColumns;
	}


}

?>