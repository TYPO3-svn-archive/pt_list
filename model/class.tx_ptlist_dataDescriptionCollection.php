<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Fabrizio Branca <branca@punkt.de>, Dorit Rottner <rottner@punkt.de>
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
/**
 * Data description collection class
 *
 * $Id$
 *
 * @author  Fabrizio Branca <branca@punkt.de>, Dorit Rottner <rottner@punkt.de>
 * @since   2009-01-15
 */



require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_objectCollection.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iSettableByArray.php';

require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_dataDescription.php';



/**
 * Data description collection class
 *
 * @author      Fabrizio Branca <branca@punkt.de>, Rainer Kuhn <kuhn@punkt.de>
 * @since       2009-01-15
 * @package     TYPO3
 * @subpackage  tx_ptlist
 */
class tx_ptlist_dataDescriptionCollection extends tx_pttools_objectCollection implements tx_pttools_iSettableByArray {

	protected $restrictedClassName = 'tx_ptlist_dataDescription';

	/**
	 * If a dataDescription added to this collection via setPropertiesFromArray() has no table defined,
	 * this defaultTable will be set. Usually the defaultTable is set from outside via set_defaultTable()
	 * and points to the first table (or its alias) used
	 *
	 * @var string
	 */
	protected $defaultTable = '';



    /***************************************************************************
     * Constructor
     **************************************************************************/

	/**
	 * Class constructor
	 *
	 * @param 	tx_ptlist_dataDescription 	(optional) first columnDescription object
	 * @param 	tx_ptlist_dataDescription 	(optional) second columnDescription object
	 * 											...
	 * @param 	tx_ptlist_dataDescription 	(optional) nth columnDescription object
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-01-22
	 */
    public function __construct() {
        foreach (func_get_args() as $arg) {
            $this->addItem($arg);
        }
    }



    /***************************************************************************
     * Overwritten methods from parent class
     **************************************************************************/

    /**
     * Adds a data description item to the collection
     *
     * @param   tx_ptlist_dataDescription data  description object
     * @throws 	tx_pttools_exception	if trying to add a data description object with key, that already exists in the collection
     * @return  void
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-21
     */
    public function addItem(tx_ptlist_dataDescription $dataDescObj) {

        if (func_num_args() > 1) {
            throw new tx_pttools_exception('Too many parameters');
        }

        $key = $dataDescObj->get_identifier();

        if ($this->hasItem($key)) {
        	throw new tx_pttools_exception(sprintf('Column "%s" already exists in collection and cannot be overwritten!', $key));
        }

        parent::addItem($dataDescObj, $key);

    }


    /***************************************************************************
     * Domain logic methods
     **************************************************************************/

    public function set_defaultTable($defaultTable) {
    	$this->defaultTable = $defaultTable;
    }

    /**
     * Enter description here...
     *
     * @return 	array	of dataDescriptionIdentifiers (string)
     */
    public function getDataDescriptionIdentifiers() {
    	$dataDescriptionIdentifiers = array();
    	foreach ($this as $dataDescription) { /* @var $dataDescription tx_ptlist_dataDescription */
    		$dataDescriptionIdentifiers[] = $dataDescription->get_identifier();
    	}
    	return $dataDescriptionIdentifiers;
    }



	/**
	 * Returns a collection of sortable columns
	 *
	 * @param 	void
	 * @return 	tx_ptlist_dataDescriptionCollection
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-01-22
	 */
	public function getSortableColumns() {

		$sortableColumns = new tx_ptlist_dataDescriptionCollection();

		foreach ($this as $dataDescription) { /* @var $dataDescription tx_ptlist_dataDescription */
			if ($dataDescription->isSortable() == true) {
				$sortableColumns->addItem($dataDescription);
			}
		}
		return $sortableColumns;
	}



	/**
	 * Returns the order by snippet for this collection's columns (regarding their sorting state)
	 *
	 * @param 	void
	 * @return 	string 	order by snippet
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-01-22
	 */
	public function getOrderByClause() {
		$orderby = array();
		foreach ($this as $column) { /* @var $column tx_ptlist_dataDescription */
			if ($column->isSortable() && $column->get_sortingState() != tx_ptlist_dataDescription::SORTINGSTATE_NONE) {
				$orderby[] = $column->getOrderByClause();
			}
		}
		return implode(', ', $orderby);
	}


	/**
	 * Return a new collection with references to the accessible columns of this collection for a given groupList
	 *
	 * @param 	string	csl of group uids
	 * @return 	tx_ptlist_dataDescriptionCollection
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-01-22
	 */
	public function getAccessibleDataDescriptions($groupList) {
		$accessibleDataDescriptions = new tx_ptlist_dataDescriptionCollection();
		foreach ($this as $dataDescription) { /* @var $dataDescription tx_ptlist_dataDescription */
			if ($dataDescription->hasAccess($groupList)) {
				$accessibleDataDescriptions->addItem($dataDescription);
			}
		}
		return $accessibleDataDescriptions;
	}
	
	





    /***************************************************************************
     * Methods implementing "tx_pttools_iSettableByArray" interface
     **************************************************************************/

	/**
	 * Set properties from array
	 *
	 * @param 	array 	dataArray
	 * @return 	void
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-01-22
	 */
	public function setPropertiesFromArray(array $dataArray) {

		// fills itself with column objects that will be constructed with the "tx_pttools_iSettableByArray" interface theirself
		foreach ($dataArray as $tsKey => $dataDescriptionConf) {

			// if the "identifier" is not set we take the typoscript key (without the dot) as identifier
			if (empty($dataDescriptionConf['identifier'])) {
				$dataDescriptionConf['identifier'] = substr($tsKey, 0, -1); /* removing the dot */
			}

			tx_pttools_assert::isNotEmptyString($dataDescriptionConf['identifier'], array('message' => sprintf('No "identifier" found in column configuration for column in key "%s"!', $tsKey)));

			if (empty($dataDescriptionConf['special'])) {

				if (empty($dataDescriptionConf['table'])) {
					$dataDescriptionConf['table'] = $this->defaultTable;
				}
				tx_pttools_assert::isNotEmptyString($dataDescriptionConf['table'], array('message' => sprintf('No "table" found in column configuration for column in key "%s"!', $tsKey)));
				tx_pttools_assert::isNotEmptyString($dataDescriptionConf['field'], array('message' => sprintf('No "field" found in column configuration for column in key "%s"!', $tsKey)));
			}

			// create a dataDescription object and add it to the collection
			$tmpDataDescription = new tx_ptlist_dataDescription($dataDescriptionConf['identifier'], $dataDescriptionConf['table'], $dataDescriptionConf['field'], $dataDescriptionConf['special']);
			$tmpDataDescription->setPropertiesFromArray($dataDescriptionConf);
			$this->addItem($tmpDataDescription);
		}

	}

	/***************************************************************************
	 * Methods implementing the "tx_pttools_iTemplateable" interface
	 **************************************************************************/

	/**
	 * Returns a marker array
	 *
	 * @param 	void
	 * @return 	array
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-01-14
	 */
	public function getMarkerArray() {
		$markerArray = array();
		foreach ($this as $column) { /* @var $column tx_ptlist_dataDescription */
			$markerArray[] = $column->getMarkerArray();
		}
		return $markerArray;
	}

}

?>