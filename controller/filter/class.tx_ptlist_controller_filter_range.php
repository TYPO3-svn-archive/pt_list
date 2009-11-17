<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2009 Rainer Kuhn (kuhn@punkt.de)
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
 * Range filter controller class for the 'pt_list' extension
 *
 * $Id$
 *
 * @author  Rainer Kuhn <kuhn@punkt.de>
 * @since   2009-01-23
 */ 



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_filter.php';
require_once t3lib_extMgm::extPath('pt_list').'view/filter/range/class.tx_ptlist_view_filter_range_userInterface.php';
require_once t3lib_extMgm::extPath('pt_list').'model/filter/filterValue/class.tx_ptlist_filterValueNumeric.php';



/**
 * Range filter controller
 *
 * @author      Rainer Kuhn <kuhn@punkt.de>
 * @since       2009-01-23
 * @package     TYPO3
 * @subpackage  tx_ptlist
 */
class tx_ptlist_controller_filter_range extends tx_ptlist_filter {
    
	
	protected $table;
	
	
	protected $field;
	
	
	
	/**
	 * Holds a reference to a filter value object
	 *
	 * @var tx_ptlist_filterValueNumeric
	 */
	protected $minFilterValue;
	
	
	
	/**
	 * Holds a referenct to a filter value object
	 *
	 * @var tx_ptlist_filterValueNumeric
	 */
	protected $maxFilterValue;
	
	
	
	/***************************************************************************
     * Construction
     **************************************************************************/
	
	/**
	 * Constructor setting up required objects
	 *
	 * @param string $listIdentifier       Identifier of list
	 * @param string $filterIdentifier     Identifier of filter
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-17
	 */
	public function __construct($listIdentifier='', $filterIdentifier='') {
		parent::__construct($listIdentifier, $filterIdentifier);
		
		$this->minFilterValue = new tx_ptlist_filterValueNumeric();
        $this->maxFilterValue = new tx_ptlist_filterValueNumeric();
	}
	
	
    /***************************************************************************
     * Domain - Logic
     * Methods defined in parent abstract class "tx_ptlist_filter" 
     **************************************************************************/
    
    /**
     * Returns the SQL WHERE clause snippet for this filter
     *
     * @param   void
     * @return  string SQL-WHERE clause
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-23
     */
    public function getSqlWhereClauseSnippet() {
        
        tx_pttools_assert::isEqual(count($this->dataDescriptions), 1, array('message' => 'This filter can only be used with 1 column'));
        
        $minRawValue = $this->minFilterValue->getRawValue();
        $maxRawValue = $this->maxFilterValue->getRawValue();
        
        $sqlSnippet = '';
        
        if (!empty($minRawValue) || !empty($maxRawValue)) {
        
	        $dbColumn = $this->table.'.'.$this->field;
	        
	        $sqlWhereClauseSnippet = array();
	        
	        if (!empty($minRawValue)) {
	        	$sqlWhereClauseSnippet[] = $dbColumn.' >= '. $this->minFilterValue->getSqlEncodedValue();
	        }
	        if (!empty($maxRawValue)) {
	        	$sqlWhereClauseSnippet[] = $dbColumn.' <= '. $this->maxFilterValue->getSqlEncodedValue();
	        }
	        $sqlSnippet = implode(' AND ', $sqlWhereClauseSnippet);
	        
        } else {
        	$sqlSnippet = 1;
        }
        
        return $sqlSnippet;
        
    }
    
    
    
    /***************************************************************************
     * Template Methods
     **************************************************************************/
    
    /**
     * Initialize the filter
     * 
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-23
     */
    public function init() {
    	parent::init();
    	
    	$this->table = $this->dataDescriptions->getItemByIndex(0)->get_table();
    	$this->field = $this->dataDescriptions->getItemByIndex(0)->get_field();
    }
    
    
    
    /**
     * Processes the filter form submission
     *
     * @param   void
     * @return  void
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-23
     */
    public function preSubmit() {
    	if ($this->params['minval'] != '') {
            $this->minFilterValue->setValue($this->params['minval']);
    	}
    	if ($this->params['maxval'] != '') {
            $this->maxFilterValue->setValue($this->params['maxval']);
    	}
    }
    
    
    
    /***************************************************************************
     * Controller action methods 
     **************************************************************************/
	
    /**
     * Displays the user interface in active state (calls isNotActiveAction in this case)
     *
     * @param   void
     * @return  string HTML output
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-23
     */
    public function isActiveAction() {
        
        // in this case we redirect to the "isActive" action as we do not want a different interface when the filter is active
        return $this->doAction('isNotActive');
        
    }
    
    
    
    /**
     * Displays the user interface in inactive state
     *
     * @param   void
     * @return  string HTML output
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-20
     */
    public function isNotActiveAction() {
        
        $view = $this->getView('filter_range_userInterface');
        $view->addItem($this->minFilterValue->getHtmlEncodedValue(), 'minval');
        $view->addItem($this->maxFilterValue->getHtmlEncodedValue(), 'maxval');
        return $view->render();
        
    }
    
    
    
    /**
     * Validating submitted filter values
     *
     * @return  boolean  Returns true, if submitted values validate
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-20
     */
    public function validate() {
    	if (TYPO3_DLOG) t3lib_div::devLog('Range validation', 'pt_list', 1, $this->minFilterValue->getRawValue() . ' ' . $this->maxFilterValue->getRawValue());
    	
    	// empty() does not work with return-values, so store them to vars here
    	$minRawValue = $this->minFilterValue->getRawValue();
    	$maxRawValue = $this->maxFilterValue->getRawValue();
   	
    	// both values have to be numeric if they are not empty
    	if (!empty($minRawValue) && !is_numeric($minRawValue)) return false;
    	if (!empty($maxRawValue) && !is_numeric($maxRawValue)) return false;
    	
    	// if both are set, the max value has to be higher than the min value
    	if (!empty($minRawValue) && !empty($maxRawValue)) {
    		if ($maxRawValue < $minRawValue) return false;
    	}
    	
    	return true;
    }



	/**
	 * This method will be called to generate the output for the filter breadcrumb.
	 *
	 * @param 	void
	 * @return 	string HTML ouput
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-06
	 */
	public function breadcrumbAction() {

		// at least one of the values has to be set
        $minRawValue = $this->minFilterValue->getRawValue();
        $maxRawValue = $this->maxFilterValue->getRawValue();
		
		if (empty($minRawValue) && empty($maxRawValue)) {
			// TODO ry21 make this translateable!
			$value = 'Not set';
		} else {
			$value = $this->minFilterValue->getHtmlEncodedValue() . '-' . $this->maxFilterValue->getHtmlEncodedValue();
		}

		$view = $this->getView('filter_breadcrumb');
		$view->addItem($this->label, 'label');
		$view->addItem($value, 'value');
		return $view->render();
	}
	
	
}

?>