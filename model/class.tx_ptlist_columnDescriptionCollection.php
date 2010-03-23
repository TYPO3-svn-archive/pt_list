<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Fabrizio Branca <mail@fabrizio-branca.de>, Rainer Kuhn <kuhn@punkt.de>
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
 * Column description collection class for the 'pt_list' extension
 *
 * $Id$
 *
 * @author  Fabrizio Branca <mail@fabrizio-branca.de>, Rainer Kuhn <kuhn@punkt.de>
 * @since   2009-01-15
 */



require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_objectCollection.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iTemplateable.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iSettableByArray.php';

require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_columnDescription.php';


/**
 * Column description collection class
 *
 * @author      Fabrizio Branca <mail@fabrizio-branca.de>, Rainer Kuhn <kuhn@punkt.de>
 * @since       2009-01-15
 * @package     TYPO3
 * @subpackage  pt_list\model
 */
class tx_ptlist_columnDescriptionCollection extends tx_pttools_objectCollection implements tx_pttools_iTemplateable, tx_pttools_iSettableByArray {

	protected $restrictedClassName = 'tx_ptlist_columnDescription';


	/**
	 * @var string 	list identifier this column description collection is attached to
	 */
	protected $listId;


    /***************************************************************************
     * Constructor
     **************************************************************************/

	/**
	 * Class constructor
	 *
	 * @param 	tx_ptlist_columnDescription 	(optional) first columnDescription object
	 * @param 	tx_ptlist_columnDescription 	(optional) second columnDescription object
	 * 											...
	 * @param 	tx_ptlist_columnDescription 	(optional) nth columnDescription object
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-22
	 */
	/*
    public function __construct() {
        foreach (func_get_args() as $arg) {
            $this->addItem($arg);
        }
    }
	*/



	/**
	 * Class constructor
	 *
	 * @param 	string	(optional) list identifier
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-23
	 */
	public function __construct($listId=NULL) {
		if (!is_null($listId)) {
			tx_pttools_assert::isNotEmptyString($listId, array('message' => 'No valid listId found!'));
			$this->listId = $listId;
		}
	}



    /***************************************************************************
     * Overwritten methods from parent class
     **************************************************************************/

    /**
     * Adds a column description item to the collection
     *
     * @param   tx_ptlist_columnDescription column  description object
     * @throws 	tx_pttools_exception	if trying to add a column with key, that already exists in the collection
     * @return  void
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-21
     */
    public function addItem(tx_ptlist_columnDescription $columnDescObj) {

        if (func_num_args() > 1) {
            throw new tx_pttools_exception('Too many parameters. The key will be set automatically from the column\'s identifier');
        }

        $key = $columnDescObj->get_columnIdentifier();

        tx_pttools_assert::isNotEmptyString($key, array('message' => 'No valid "identifier" found in columnDescription object.'));

        if ($this->hasItem($key)) {
        	throw new tx_pttools_exception(sprintf('Column "%s" already exists in collection and cannot be overwritten!', $key));
        }

        parent::addItem($columnDescObj, $key);

    }


    /***************************************************************************
     * Domain logic methods
     **************************************************************************/

	/**
	 * Returns a collection of sortable columns
	 *
	 * @param 	void
	 * @return 	tx_ptlist_columnDescriptionCollection
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-22
	 */
	public function getSortableColumns() {

		$sortableColumns = new tx_ptlist_columnDescriptionCollection($this->listId);

		foreach ($this as $column) { /* @var $column tx_ptlist_columnDescription */
			if ($column->isSortable() == true) {
				$sortableColumns->addItem($column);
			}
		}
		return $sortableColumns;
	}



	/**
	 * Returns a comma-separated list of all fields this collection's columns contain
	 *
	 * This methods returns an array:
	 *
	 * $fields = array(
	 * 		'<columnIdentifier>' => array(
	 * 			'<dataDescriptionIdentifier> => array(
	 * 				'table' => '<tableName>',
	 * 				'field' => '<fieldName>'
	 * 			)
	 * 			...
	 * 			'<dataDescriptionIdentifier> => array(
	 * 			)
	 * 		),
	 * 		...
	 * 		'<columnIdentifier>' => array(
	 * 		),
	 * )
	 *
	 * @param 	void
	 * @return 	array	field name information (see above for information on array structure)
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-22
	 */
	public function getFieldList() {

		throw new tx_pttools_exception('Is this method used?');

		$fields = array();
		foreach ($this as $column) { /* @var $column tx_ptlist_columnDescription */
			foreach ($column->get_dataDescriptions() as $dataDescription) { /* @var $dataDescription tx_ptlist_dataDescription */
				$fields[$column->get_columnIdentifier()][$dataDescription->get_identifier()] = array(
					'table' => $dataDescription->get_table(),
					'field' => $dataDescription->get_field(),
					'special' => $dataDescription>get_special(),
				);
			}
		}
		return $fields;
	}

	
	
    /**
     * Returns the select snippet for the SQL clause
     *
     * @param   void
     * @return  string
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-02-02
     */
    public function getSelectClause() {
    	
    	$listObject = tx_pttools_registry::getInstance()->get($this->listId.'_listObject'); /* @var $listObject tx_ptlist_list */

    	$selectSnippetArray = array();
    	foreach ($this as $column) { /* @var $column tx_ptlist_columnDescription */
			foreach ($column->get_dataDescriptions() as $dataDescription) { /* @var $dataDescription tx_ptlist_dataDescription */
				if (!isset($selectSnippetArray[$dataDescription->get_identifier()])) {
					$selectSnippetArray[$dataDescription->get_identifier()] = $dataDescription->getSelectClause();
				}
			}
			
			$sortingDataDescriptions = t3lib_div::trimExplode(',', $column->get_sortingDataDescription(), true);
			foreach ($sortingDataDescriptions as $sortingDataDescriptionIdentifier) {
				if (!isset($selectSnippetArray[$sortingDataDescriptionIdentifier])) {
					// remove asc, desc, !asc or !desc
					list($sortingDataDescriptionIdentifier) = t3lib_div::trimExplode(' ', $sortingDataDescriptionIdentifier);
					$sortingDataDescription = $listObject->getAllDataDescriptions()->getItemById($sortingDataDescriptionIdentifier);
					$selectSnippetArray[$sortingDataDescriptionIdentifier] = $sortingDataDescription->getSelectClause();
				}
			}
		}
		
        $selectSnippet = implode(', ', $selectSnippetArray); /* @var $selectSnippet string */

        return $selectSnippet;
    }

    

	/**
	 * Returns the order by snippet for this collection's columns (regarding their sorting state)
	 *
	 * @param 	void
	 * @return 	string 	order by snippet
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-22
	 */
	public function getOrderByClause() {
		$orderby = array();
		foreach ($this as $column) { /* @var $column tx_ptlist_columnDescription */
			if ($column->isSortable() && ($column->get_sortingState() != tx_ptlist_columnDescription::SORTINGSTATE_NONE)) {
				$orderby[] = $column->getOrderByClause();
			}
		}
		return implode(', ', $orderby);
	}

	

	/**
	 * Return a new collection with references to the accessible columns of this collection for a given groupList
	 *
	 * @param 	string	csl of group uids
	 * @return 	tx_ptlist_columnDescriptionCollection
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-22
	 */
	public function getAccessibleColumns($groupList) {
		$accessibleColumns = new tx_ptlist_columnDescriptionCollection($this->listId);
		foreach ($this as $column) { /* @var $column tx_ptlist_columnDescription */
			if ($column->hasAccess($groupList)) {
				$accessibleColumns->addItem($column);
			}
		}
		return $accessibleColumns;
	}



	/**
	 * Return a new collection with references to the not hidden columns of this collection
	 *
	 * @param 	void
	 * @return 	tx_ptlist_columnDescriptionCollection
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-10
	 */
	public function removeHiddenColumns() {
		$notHiddenColumns = new tx_ptlist_columnDescriptionCollection($this->listId);
		foreach ($this as $column) { /* @var $column tx_ptlist_columnDescription */
			if ($column->get_hidden() == false) {
				$notHiddenColumns->addItem($column);
			}
		}
		return $notHiddenColumns;
	}





    /***************************************************************************
     * Methods implementing "tx_pttools_iSettableByArray" interface
     **************************************************************************/

	/**
	 * Set properties from array
	 *
	 * @param 	array 	dataArray
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-22
	 */
	public function setPropertiesFromArray(array $dataArray) {

		tx_pttools_assert::isNotEmptyString($this->listId, array('message' => 'No "listId" set.'));

		// first sort array by keys (as we expect single column definitions defined under keys 10., 20., 30., ...)
		ksort($dataArray);

		// fills itself with column objects that will be constructed with the "tx_pttools_iSettableByArray" interface theirself
		foreach ($dataArray as $tsKey => $columnConf) {

			// check if required key "columnIdentifier" is set
			tx_pttools_assert::isNotEmptyString($columnConf['columnIdentifier'], array('message' => sprintf('No "columnIdentifier" found in column configuration for column in key "%s"!', $tsKey)));
			$columnConf['listIdentifier'] = $this->listId;

			$this->addItem(new tx_ptlist_columnDescription($columnConf));
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
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-14
	 */
	public function getMarkerArray() {
		$markerArray = array();
		foreach ($this as $key => $column) { /* @var $column tx_ptlist_columnDescription */
			$markerArray[$key] = $column->getMarkerArray();
		}
		return $markerArray;
	}

}

?>