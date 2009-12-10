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

require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_objectCollection.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iTemplateable.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iSettableByArray.php';

require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_filter.php';


/**
 * Filter collection class
 *
 * @version 	$Id$
 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
 * @since		2009-01-19
 */
class tx_ptlist_filterCollection extends tx_pttools_objectCollection implements tx_pttools_iTemplateable, Serializable, tx_pttools_iSettableByArray {

	protected $restrictedClassName = 'tx_ptlist_filter';

	/**
	 * @var string 	list identifier this filter collection is attached to
	 */
	protected $listId;



	/**
	 * Class constructor
	 *
	 * @param 	string	list identifier
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-23
	 */
	public function __construct($listId) {
		tx_pttools_assert::isNotEmptyString($listId, array('message' => 'No valid listId found!'));
		$this->listId = $listId;
	}



    /**
     * Adds a filter item to the collection and checks if the key already exists
     *
     * @param   tx_ptlist_filter    filter object
     * @throws 	tx_pttools_exception	if trying to add a filter with key, that already exists in the collection
     * @return  void
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-21
     */
    public function addItem(tx_ptlist_filter $filterObj) {

        if (func_num_args() > 1) {
            throw new tx_pttools_exception('Too many parameters');
        }

        $key = $filterObj->get_filterIdentifier();

        if ($this->hasItem($key)) {
        	throw new tx_pttools_exception(sprintf('Filter "%s" already exists in collection and cannot be overwritten!', $key));
        }

        parent::addItem($filterObj, $key);

    }



	/**
	 * Filters out all filters that aren't active
	 * and returns a new object collection with references to the original objects
	 *
	 * @param void
	 * @return tx_ptlist_filterCollection
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since 2009-05-09
	 */
	public function where_isActive() {
		$collection = new tx_ptlist_filterCollection($this->listId);

		foreach ($this as $key => $filter) { /* @var filter tx_ptlist_filter */
			if ($filter->get_isActive() == true) {
				$collection->addItem($filter);
			}
		}

		return $collection;
	}



    /**
	 * Return a filterCollection containing references to those filters that are accessible by the current user
	 *
	 * @param 	string	group list
	 * @return 	tx_ptlist_filterCollection
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-23
	 */
	public function getAccessibleFilters($groupList) {
		$accessibleFilters = new tx_ptlist_filterCollection($this->listId);
		foreach ($this as $filter) { /* @var $filter tx_ptlist_filter */
			if ($filter->hasAccess($groupList)) {
				$accessibleFilters->addItem($filter);
			}
		}
		return $accessibleFilters;
	}



	/**
	 * Return a filterCollection containing references to those filters that belong to a given filterbox
	 *
	 * @param 	string	group list
	 * @return 	tx_ptlist_filterCollection
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-23
	 */
	public function getFiltersForFilterbox($filterboxId) {
		$filterCollection = new tx_ptlist_filterCollection($this->listId);
		foreach ($this as $filter) { /* @var $filter tx_ptlist_filter */
			if ($filter->get_filterboxIdentifier() == $filterboxId) {
				$filterCollection->addItem($filter);
			}
		}
		return $filterCollection;
	}



	/**
	 * Processes all controllers that have a user interface (each filter is a controller)
	 *
	 * @param 	void
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	public function processSubControllers() {
		foreach ($this as $filter) { /* @var $filter tx_ptlist_filter */
			$filter->main();
		}
	}



	/**
	 * Returns a marker array
	 *
	 * @return 	array
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-15
	 */
	public function getMarkerArray() {
		$markerArray = array();
		foreach ($this as $key => $filter) { /* @var $filter tx_ptlist_filter */
			if ($filter->get_hasUserInterface()) {
				$markerArray[$key] = $filter->getMarkerArray();
			}
		}
		return $markerArray;
	}



	/**
	 * Resets all filters
	 *
	 * @param	string	(optional) csl of filterIdentifier not to be resetted
	 * @return 	tx_ptlist_filterCollection
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-16
	 */
	public function reset($except='') {
		$except = t3lib_div::trimExplode(',', $except);
		foreach ($this as $filter) { /* @var $filter tx_ptlist_filter */
			if (!in_array($filter->get_filterIdentifier(), $except)) {
				$filter->reset();
			}
		}
		return $this;
	}



	/**
	 * Returns the where clause snippet for all filters
	 *
	 * @return 	string	csl of filter identifiers to ignore (the filter itself will be ignored by default if active)
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	public function getSqlWhereClauseSnippet($ignoredFiltersForWhereClause='') {
		$ignoredFilterIdentifiers = t3lib_div::trimExplode(',', $ignoredFiltersForWhereClause);
		$whereClauses = array();
		foreach ($this as $filter) { /* @var $filter tx_ptlist_filter */
			if ($filter->get_isActive() && !in_array($filter->get_filterIdentifier(), $ignoredFilterIdentifiers)) {
				$whereClauses[] = ($filter->get_invert() ? 'NOT ' : ''). '(' . $filter->getSqlWhereClauseSnippet() . ')';
			}
		}
		$whereClause = implode(' AND ', $whereClauses);
		return $whereClause;
	}



	/**
	 * Get all filterbox ids
	 *
	 * @param 	void
	 * @return 	array	array of filterbox ids
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-27
	 */
	public function getFilterboxIds() {
		$filterboxIds = array();
		foreach ($this as $filter) { /* @var $filter tx_ptlist_filter */
			if (!in_array($filter->get_filterboxIdentifier(), $filterboxIds)) {
				$filterboxIds[] = $filter->get_filterboxIdentifier();
			}
		}
		return $filterboxIds;
	}



	/**
	 * Accumulate all filter values into  parameter string
	 *
	 * @param string csl of filter identifiers not to be included into the string
	 * @return string
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-10-01
	 */
	public function getAllFilterValueAsGetParameterString($ignoredFilters = '') {
		$ignoredFilterIdentifiers = t3lib_div::trimExplode(',', $ignoredFilters);
		$parameterString = '';
		/* @var $filter tx_ptlist_filter */
		foreach ($this as $filter) {
			if (!in_array($filter->get_filterIdentifier(), $ignoredFilterIdentifiers)) {
				$parameterString .= $filter->getFilterValueAsGetParameterString();
			}
		}
		return $parameterString;
	}



    /***************************************************************************
     * Methods implementing "tx_pttools_iSettableByArray" interface
     **************************************************************************/

	/**
	 * Set properties from array
	 * Expects following array
	 * array(
	 * 		'<filterboxId>.' => array(
	 * 			'10' => <filterClass>
	 * 			'10.' => <filterConfigurationArray>, // for details regarding the filterConfigurationArray see comments in "tx_ptlist_filter->setPropertiesFromArray()"
	 * 			...
	 *			'n' => <filterClass>
	 * 			'n.' => <filterConfigurationArray>,
	 * 		),
	 * 		...
	 * 		'<anotherFilterboxId>.' => array(
	 *			'10' => <filterClass>
	 * 			'10.' => <filterConfigurationArray>,
	 * 			...
	 * 			'n' => <filterClass>
	 * 			'n.' => <filterConfigurationArray>,
	 * 		),
	 * );
	 *
	 * @param 	array 	dataArray
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-22
	 */
	public function setPropertiesFromArray(array $dataArray) {

		// loop over all filterboxes
		foreach ($dataArray as $tsKey => $filterboxConfiguration) { /* @var $filterboxConfiguration array */

			tx_pttools_assert::isNotEmptyArray($filterboxConfiguration, array('message' => sprintf('No filterbox configuration found in key "%s"', $tsKey)));

			// retrieve filterboxId from array key
			tx_pttools_assert::isEqual(substr($tsKey, -1), '.');
			$filterboxId = substr($tsKey, 0, -1); /* removing the dot */

			if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Processing configuration for filterbox "%s"', $filterboxId), 'pt_list');

			// first sort array by keys (as we expect single column definitions defined under keys 10., 20., 30., ...)
			$sortedKeys = t3lib_TStemplate::sortedKeyList($filterboxConfiguration, false);

			// loop over all single filter configurations in the current filterbox
			foreach ($sortedKeys as $tsKey) {

				$filterClass = $filterboxConfiguration[$tsKey];
				tx_pttools_assert::isNotEmptyString($filterClass, array('message' => sprintf('No filterClass defined for filter in key "%s" in filterbox "%s"', $tsKey, $filterboxId)));

				$filterConf = $filterboxConfiguration[$tsKey.'.'];
				tx_pttools_assert::isNotEmptyArray($filterConf, array('message' => sprintf('No filter configuration found for filter in key "%s" in filterbox "%s"', $tsKey, $filterboxId)));

				tx_pttools_assert::isNotEmptyString($filterConf['filterIdentifier'], array('message' => sprintf('No filter identifier found for filter in key "%s" in filterbox "%s"', $tsKey, $filterboxId)));


				// check if class file exists
				$file = implode(':', array_slice(t3lib_div::trimExplode(':', $filterClass), 0, -1));
				tx_pttools_assert::isFilePath($file, array('message' => sprintf('File "%s" not found', $file)));

				// construct object
				$tmpFilter = t3lib_div::getUserObj($filterClass); /* @var $tmpFilter tx_ptlist_filter */
				tx_pttools_assert::isInstanceOf($tmpFilter, 'tx_ptlist_filter', array('message' => 'Created object is not an instance of "tx_ptlist_filter"'));

				// set/overwrite listIdentifier into filter configuration array
				$filterConf['listIdentifier'] = $this->listId;
				$filterConf['filterboxIdentifier'] = $filterboxId;

				$tmpFilter->setPropertiesFromArray($filterConf);

				$this->addItem($tmpFilter);
			}
		}
	}


	/***************************************************************************
	 * Methods implementing the "Serializable" interface
	 **************************************************************************/

	/**
	 * Returns a "safe" string representation of this object
	 * This method will automatically executed when calling serialize($thisObject)
	 *
	 * @return 	string	string representation
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-09
	 */
	public function serialize() {
		$serializedFilters = array();
		foreach ($this as $filter) { /* @var $filter tx_ptlist_filter */
			$serializedFilters[] = serialize($filter);
		}
		return serialize($serializedFilters);
	}



	/**
	 * Converts a string representation into the original object
	 * This method will automatically executed when calling unserialize($thisObject)
	 *
	 * @param 	string 	string representation
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	public function unserialize($serialized) {
		tx_pttools_assert::isString($serialized);
		$serializedFilters = unserialize($serialized);
		tx_pttools_assert::isArray($serializedFilters);
		foreach ($serializedFilters as $key => $serializedFilter) { /* @var $serializedFilter string */
			$filterObj = unserialize($serializedFilter);
			if ($filterObj instanceof tx_ptlist_filter) {
				$this->addItem($filterObj);
			} else {
				if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Could not unserialize filter "%s"', $key), 'pt_list', 2, $serializedFilter);
			}
		}
	}

}

?>