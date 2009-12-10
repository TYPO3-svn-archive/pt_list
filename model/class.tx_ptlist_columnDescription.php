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
 * Column description class for the 'pt_list' extension
 *
 * $Id$
 *
 * @author  Fabrizio Branca <mail@fabrizio-branca.de>, Rainer Kuhn <kuhn@punkt.de>
 * @since   2009-01-15
 */


/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iTemplateable.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iSettableByArray.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_div.php';

require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_div.php';



/**
 * Column description class
 *
 * @author      Fabrizio Branca <mail@fabrizio-branca.de>, Rainer Kuhn <kuhn@punkt.de>
 * @since       2009-01-15
 * @package     TYPO3
 * @subpackage  tx_ptlist
 */
class tx_ptlist_columnDescription implements tx_pttools_iTemplateable, tx_pttools_iSettableByArray {

    /***************************************************************************
     * Properties
     **************************************************************************/

	/**
	 * @var string
	 */
	protected $columnIdentifier;

	/**
	 * @var bool
	 */
	protected $isSortable = true;  // true by default, use setter to change

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * @var string
	 */
	protected $sortingDataDescription;

	/**
	 * @var int holds the sorting state of this column
	 */
	protected $sortingState;

	/**
	 * @var string	comma-separated list if fe_group uids that have access to this column
	 */
	protected $access;

	/**
	 * @var tx_ptlist_dataDescriptionCollection
	 */
	protected $dataDescriptions;

	/**
	 * @var string
	 */
	protected $listIdentifier;

	/**
	 * @var bool
	 */
	protected $hidden = false;

	/**
	 * @var array	keys "renderUserFunctions.", "renderObj", "renderObj."
	 */
	protected $renderConfig = array();



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

	public function __construct(array $dataArray = array()) {
		$this->dataDescriptions = new tx_ptlist_dataDescriptionCollection();
		if (!empty($dataArray)) {
			$this->setPropertiesFromArray($dataArray);
		}
	}

    /***************************************************************************
     * Domain logic methods
     **************************************************************************/

	/**
	 * Render field content
	 *
	 * @param 	array	values
	 * @return	string	rendered field content
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-20
	 */
	public function renderFieldContent(array $values) {
		return tx_ptlist_div::renderValues($values, $this->renderConfig);
	}



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
		tx_pttools_assert::isInArray($this->sortingState, array(self::SORTINGSTATE_DESC, self::SORTINGSTATE_ASC), array('message' => 'Invalid sorting state (must be "tx_ptlist_columnDescription::SORTINGSTATE_DESC" or "tx_ptlist_columnDescription::SORTINGSTATE_ASC")'));
        tx_pttools_assert::isNotEmptyString($this->sortingDataDescription, array('message'=>'No sorting data description set'));

		$sortingData = t3lib_div::trimExplode(',', $this->sortingDataDescription);
		foreach ($sortingData as &$value) {
			list($dataDescriptionIdentifier, $direction) = t3lib_div::trimExplode(' ', $value);

			switch(strtolower($direction)) {
				case '':
				case 'asc': {
					$direction = self::SORTINGSTATE_ASC * $this->sortingState; // this will be inverted depending on the sorting state of the column
				} break;
				case 'desc': {
					$direction = self::SORTINGSTATE_DESC * $this->sortingState; // this will be inverted depending on the sorting state of the column
				} break;
				case '!asc': {
					$direction = self::SORTINGSTATE_ASC; // this will NOT be inverted depending on the sorting state of the column
				} break;
				case '!desc': {
					$direction = self::SORTINGSTATE_DESC; // this will NOT be inverted depending on the sorting state of the column
				} break;
				default: {
					throw new tx_pttools_exception(sprintf('"%s" is an invalid sorting direction!', $direction));
				}
			}

			$value = $dataDescriptionIdentifier . ' ' . ($direction == self::SORTINGSTATE_DESC ? 'DESC' : 'ASC');
		}

		return implode(', ', $sortingData);
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

        // check if display column itself is sortable
        if ($this->isSortable == false) {
            return false;
        }

        // check if the column's sorting data descriptions are sortable
        tx_pttools_assert::isNotEmptyString($this->sortingDataDescription, array('message'=>'No sortingDataDescription found!'));
        tx_pttools_assert::isNotEmptyString($this->listIdentifier, array('message'=>'No listIdentifier found!'));
        $listObject = tx_pttools_registry::getInstance()->get($this->listIdentifier.'_listObject'); /* @var $listObject tx_ptlist_list */
        tx_pttools_assert::isInstanceOf($listObject, 'tx_ptlist_list', array('message'=>'Could not find a list object in the registry!'));

        foreach (t3lib_div::trimExplode(',', $this->sortingDataDescription) as $sortingDataDescriptionIdentifier) { /* @var $sortingDataDescriptionIdentifier string */
        	list($sortingDataDescriptionIdentifier, $direction) = t3lib_div::trimExplode(' ', $sortingDataDescriptionIdentifier);
            if (!$listObject->getAllDataDescriptions()->hasItem($sortingDataDescriptionIdentifier)) {
                throw new tx_pttools_exception(sprintf('Could not find dataDescriptionIdentifier "%s" in list "%s" in column "%s"', $sortingDataDescriptionIdentifier, $this->listIdentifier, $this->columnIdentifier));
            }
            if ($listObject->getAllDataDescriptions()->getItemById($sortingDataDescriptionIdentifier)->isSortable() == false) {
                return false;
            }
        }

        return true;
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

        // check display column access
        if (tx_pttools_div::hasGroupAccess($groupList, $this->access) == false) {
            return false;
        }

        // check access for the column's data descriptions
        foreach ($this->dataDescriptions as $dataDescription) { /* @var $dataDescription tx_ptlist_dataDescription */
            if ($dataDescription->hasAccess($groupList) == false) {
                return false;
            }
        }

        return true;

    }



    /**
     * Get sorting data descriptions
     *
     * @param 	void
     * @return	string 	csl of dataDescriptionIdentifiers
     */
    public function get_sortingDataDescription() {
    	return $this->sortingDataDescription;
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
	public function setPropertiesFromArray(array $dataArray) {
		if (isset($dataArray['listIdentifier'])) {
			$this->listIdentifier = $dataArray['listIdentifier'];
		}
		if (isset($dataArray['columnIdentifier'])) {
			$this->columnIdentifier = $dataArray['columnIdentifier'];
		}
		if (isset($dataArray['isSortable'])) {
			$this->isSortable = (bool) $dataArray['isSortable'];
		}
		if (isset($dataArray['label'])) {
			$this->label = $GLOBALS['TSFE']->cObj->stdWrap($dataArray['label'], $dataArray['label.']);
		}
		if (isset($dataArray['access'])) {
			$this->access = $dataArray['access'];
		}
		if (isset($dataArray['dataDescriptionIdentifier'])) {
			$dataDescriptionIdentifiers = array();
			$listObject = tx_pttools_registry::getInstance()->get($this->listIdentifier.'_listObject'); /* @var $listObject tx_ptlist_list */
			if ($dataArray['dataDescriptionIdentifier'] == '*') {
				foreach($listObject->getAllDataDescriptions()->getAccessibleDataDescriptions($GLOBALS['TSFE']->gr_list) as $dataDescription) { /* @var $dataDescription tx_ptlist_dataDescription */
					$dataDescriptionIdentifiers[] = $dataDescription->get_identifier();
				}
			} else {
				$dataDescriptionIdentifiers = t3lib_div::trimExplode(',', $dataArray['dataDescriptionIdentifier']);
			}
			foreach ($dataDescriptionIdentifiers as $dataDescriptionIdentifier) {
				if (!$listObject->getAllDataDescriptions()->hasItem($dataDescriptionIdentifier)) {
					throw new tx_pttools_exception(sprintf('Could not find dataDescriptionIdentifier "%s" in list "%s" in column "%s"', $dataDescriptionIdentifier, $this->listIdentifier, $this->columnIdentifier));
				}
				$this->dataDescriptions->addItem($listObject->getAllDataDescriptions()->getItemById($dataDescriptionIdentifier));
			}
		}
        if (isset($dataArray['sortingDataDescription'])) {
            $this->sortingDataDescription = $dataArray['sortingDataDescription'];
        }
        if (empty($this->sortingDataDescription)) {
            $this->sortingDataDescription = $this->dataDescriptions->getItemByIndex(0)->get_identifier();
        }

		// Rendering configuration
		// Fields will be rendered with the tx_ptlist_div::renderValues() method. Have a look at the comment there for details
		if (isset($dataArray['renderObj']) && isset($dataArray['renderObj.'])) {
			$this->renderConfig['renderObj'] = $dataArray['renderObj'];
			$this->renderConfig['renderObj.'] = $dataArray['renderObj.'];
		}
		if (isset($dataArray['renderUserFunctions.'])) {
			$this->renderConfig['renderUserFunctions.'] = $dataArray['renderUserFunctions.'];
		}
	}


    /***************************************************************************
     * Methods implementing the "tx_pttools_iTemplateable" interface
     **************************************************************************/

	/**
	 * Get marker array
	 *
	 * @param 	void
	 * @return 	array	marker array
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-23
	 */
	public function getMarkerArray() {
		$markerArray = array(
			'identifier' => $this->columnIdentifier,
			'label' => $this->label,
			'isSortable' => $this->isSortable(),
			'sortingState' => $this->sortingState,
		);
		return $markerArray;
	}



    /***************************************************************************
     * Getter/setter methods
     **************************************************************************/

    public function get_columnIdentifier() {
        return $this->columnIdentifier;
    }

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
     * Get the hidden flag
     * 
     * @oaram void
     * @return bool
     */
    public function get_hidden() {
    	return $this->hidden;
    }
    
    

    /**
     * Set the hidden flag
     * 
     * @param $hidden
     * @return void
     */
    public function set_hidden($hidden) {
    	$this->hidden = (bool)$hidden;
    }

    
    
    /**
     * Sets the label property
     *
     * @param   string
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-21
     */
    public function set_label($label) {
        tx_pttools_assert::isString($label);
        $this->label = $label;
    }

    
    
    /**
     * Get the sorting state
     * 
     * @param void
     * @return string sorting state, see class constants for available values
     */
    public function get_sortingState() {
        tx_pttools_assert::isTrue($this->isSortable(), array('message' => 'Sorting is not supported for this column!'));
        return $this->sortingState;
    }



    /**
     * Set the sorting state
     *
     * @param 	int		sorting state, see class constants for available values
     * @return 	void
     * @author	Fabrizio Branca <mail@fabrizio-branca.de>
     * @since	2009-02-23
     */
    public function set_sortingState($sortingState) {
        tx_pttools_assert::isTrue($this->isSortable(), array('message' => 'Sorting is not supported for this column!'));
        $this->sortingState = $sortingState;
    }



    /**
     * Get a collection of dataDescriptions used by this column
     *
     * @param	void
     * @return  tx_ptlist_dataDescriptionCollection
     * @author	Fabrizio Branca <mail@fabrizio-branca.de>
     * @since	2009-02-23
     */
    public function get_dataDescriptions() {
    	return $this->dataDescriptions;
    }
}

?>