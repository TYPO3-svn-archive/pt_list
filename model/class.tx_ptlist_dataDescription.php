<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Fabrizio Branca (mail@fabrizio-branca.de), Dorit Rottner (rottner@punkt.de)
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

require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iSettableByArray.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_div.php';


/**
 * Data description class
 *
 * @author      Fabrizio Branca <mail@fabrizio-branca.de>, Dorit Rottner <rottner@punkt.de>
 * @since       2009-01-30
 * @package     TYPO3
 * @subpackage  tx_ptlist
 */
class tx_ptlist_dataDescription implements tx_pttools_iSettableByArray {

    /***************************************************************************
     * Properties
     **************************************************************************/

	/**
	 * @var string
	 */
	protected $identifier;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $field;

	/**
	 * @var bool
	 */
	protected $isSortable = true;  // true by default, use setter to change

	/**
	 * @var string	comma-separated list if fe_group uids that have access to this column
	 */
	protected $access;

	/**
	 * @var string
	 */
	protected $special;



    /***************************************************************************
     * Constants
     **************************************************************************/

    /**
     * @var integer
     */
	const SORTINGSTATE_NONE = 0;

	/**
     * @var integer
     */
	const SORTINGSTATE_ASC = 1;

	/**
     * @var integer
     */
	const SORTINGSTATE_DESC = -1;



    /***************************************************************************
     * Constructor
     **************************************************************************/

	/**
	 * Constructor
	 *
     * @param   string  dataDescription identifier
     * @param   string  related database table
     * @param   string  related database field in table
     * @return  void
	 * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-21
	 *
	 */
	public function __construct($identifier, $table, $field, $special=NULL) {

		tx_pttools_assert::isNotEmptyString($identifier, array('message' => 'No valid "identifier" set.'));

		if (is_null($special)) {
			tx_pttools_assert::isNotEmptyString($table, array('message' => 'No valid "table" set.'));
			tx_pttools_assert::isNotEmptyString($field, array('message' => 'No valid "field" set.'));
			$this->table = $table;
			$this->field = $field;
		} else {
			$this->special = $special;
		}
		$this->identifier = $identifier;
	}


    /***************************************************************************
     * Domain logic methods
     **************************************************************************/

	/**
	 * Returns the orderby clause snippet for this column (if sortable)
	 *
	 * @param 	void
	 * @return 	string orderby clause snippet
	 * @throws	tx_pttools_exceptionAssertion	if column is not sortable
	 * @throws	tx_pttools_exception			if sortingState has an invalid value
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	public function getOrderByClause() {

		tx_pttools_assert::isTrue($this->isSortable, array('message' => 'This column is not sortable'));
		switch ($this->sortingState) {
			case self::SORTINGSTATE_NONE: {
				$orderBy = '';
			} break;

			case self::SORTINGSTATE_ASC: {
				$orderBy = sprintf('%s.%s %s', $this->table, $this->field, 'ASC');
			} break;

			case self::SORTINGSTATE_DESC: {
				$orderBy = sprintf('%s.%s %s', $this->table, $this->field, 'DESC');
			} break;

			default: {
				throw new tx_pttools_exception('Invalid sorting state!');
			} break;
		}
		return $orderBy;
	}


	
	/**
	 * Returns the select clause snippet
	 *
	 * @param	void
	 * @return	string	select clause snippet
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-18
	 */
	public function getSelectClause($includeIdentifier = true) {
		$select = '';
		if (!empty($this->special)) {
			$select .= $this->special;
		} else {
			$select .= $this->table . '.' . $this->field;
		}
		if ($includeIdentifier == true) {
			$select .= ' AS ' . $this->identifier;
		}
		return $select;
	}



	/**
	 * Return if this column is sortable
	 *
	 * @param 	void
	 * @return 	bool
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-22
	 */
	public function isSortable() {
		return $this->isSortable;
	}



	/**
	 * Return if a user has access to this column
	 *
	 * @param 	string	csl of group uid
	 * @return 	bool	true if the user has access to this object
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-22
	 */
    public function hasAccess($groupList) {
        return tx_pttools_div::hasGroupAccess($groupList, $this->access);
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
	 * @since	2009-01-21
	 */
	public function setPropertiesFromArray(array $dataDescriptionArray) {
		if (isset($dataDescriptionArray['isSortable'])) {
			$this->isSortable = (bool) $dataDescriptionArray['isSortable'];
		}
		if (isset($dataDescriptionArray['access'])) {
			$this->access = $dataDescriptionArray['access'];
		}
	}


    /***************************************************************************
     * Methods implementing the "tx_pttools_iTemplateable" interface
     **************************************************************************/

	/*
	 * TODO: the dataDescription class does not implement the tx_pttools_iTemplateable anymore
	 * have a look a the columnDescription
	 *
	public function getMarkerArray() {

		$markerArray = array(
			'identifier' => $this->identifier,
			'label' => $this->label,
			'isSortable' => $this->isSortable,
			'sortingState' => $this->sortingState,
		);
		return $markerArray;

	}
	*/



    /***************************************************************************
     * Getter/setter methods
     **************************************************************************/

	/**
	 * Get the identifier
	 * 
	 * @param void
	 * @return string identifier
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
	 */
    public function get_identifier() {
        return $this->identifier;
    }

    
    
    /**
     * Get access settings
     * 
     * @param void
     * @return string access settings
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
     */
    public function get_access() {
        return $this->access;
    }

    
    
    /**
     * Sets the access property
     *
     * @param   string  comma-separated list if fe_group uids that have access to this column
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-21
     */
    public function set_access($access) {
        tx_pttools_assert::isString($access, array('message' => 'CSL string expected!'));
        $this->access = $access;
    }

    
    
    /**
     * Sets the isSortable property
     *
     * @param   bool
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-21
     */
    public function set_isSortable($isSortable) {
        tx_pttools_assert::isBoolean($isSortable);
        $this->isSortable = $isSortable;
    }

    
    
    /**
     * Get field property
     * 
     * @param void
     * @return string field
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
     */
    public function get_field() {
        return $this->field;
    }

    
    
    /**
     * Get table property
     * 
     * @param void
     * @return string table
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
     */
    public function get_table() {
        return $this->table;
    }

    
    
    /**
     * Get special property
     * 
     * @param void
     * @return string special
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
     */
    public function get_special() {
    	return $this->special;
    }

}

?>