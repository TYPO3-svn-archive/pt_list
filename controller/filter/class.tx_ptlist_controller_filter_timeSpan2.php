<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Michael Knoll (knoll@punkt.de)
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
 * Class file definition for timespan filter
 * 
 * @author   Michael Knoll
 * @since    2009-07-17
 * @version  $ID:$
 */



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_filter.php';
require_once t3lib_extMgm::extPath('pt_list').'view/filter/timeSpan2/class.tx_ptlist_view_filter_timeSpan2_userInterface.php';
require_once t3lib_extMgm::extPath('pt_list').'model/filter/filterValue/class.tx_ptlist_filterValueDate.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_exception.php';



/**
 * Class implementing a "timespan" filter
 *
 * @package     Typo3
 * @subpackage  pt_list
 * @author		Michael Knoll <knoll@punkt.de>
 * @since		2009-07-17
 */
class tx_ptlist_controller_filter_timeSpan2 extends tx_ptlist_filter {

    /**
     * Holds a reference to a filter-value date object for from date
     *
     * @var tx_ptlist_filterValueDate
     */	
	protected $fromDate;
	
	
	
	/**
	 * Holds a reference to a filter-value date object for to date
	 *
	 * @var tx_ptlist_filterValueDate
	 */
	protected $toDate;
	
	
	
	/**
	 * input format of date value
	 *
	 * @var string
	 */
	protected $inputFormat = tx_ptlist_filterValueDate::DD_DOT_MM_DOT_YYYY_INPUT_FORMAT;
	
	
	
	/**
	 * output format of date value
	 *
	 * @var string
	 */
	protected $outputFormat = tx_ptlist_filterValueDate::DD_DOT_MM_DOT_YYYY_OUTPUT_FORMAT;
	
	
	
	/**
	 * output format for sql output
	 *
	 * @var string
	 */
	protected $dateFieldType = 'timestamp';

	
	
	/****************************************************************************************************************
     * Modifying MVC functionality
     ****************************************************************************************************************/
	
    /**
     * MVC init method:
     * Checks if the column collection contains exactly one column as this filter can be used only with one column at the same time
     *
     * @param   void
     * @return  void
     * @throws  tx_pttools_exceptionAssertion   if more than one column is attached to the filters columnCollection
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-01-23
     */
    public function init() {
        
    	// Make sure, pt_jqueryui extension is installed
        if (!t3lib_extMgm::isLoaded('pt_jqueryui')) {
            throw new tx_pttools_exception('You need to install and load pt_jqueryui to run datepicker filter!');
        }
    	
        parent::init();
        
        // Ensure filter is only configured for 1 data description
        tx_pttools_assert::isEqual(count($this->dataDescriptions), 1, array('message' => sprintf('This filter can only be used with 1 dataDescription (dataDescription found: "%s"', count($this->dataDescriptions))));
        
        // Load some configuration from TS
        // TODO write some documentation here!
        $this->inputFormat   = $this->conf['inputFormat']   == '' ? $this->inputFormat   : $this->conf['inputFormat'];
        $this->outputFormat  = $this->conf['outputFormat']  == '' ? $this->outputFormat  : $this->conf['outputFormat'];
        $this->dateFieldType = $this->conf['dateFieldType'] == '' ? $this->dateFieldType : $this->conf['dateFieldType'];
        
    }
    
	
	
	/****************************************************************************************************************
	 * Action Methods
	 ****************************************************************************************************************/
	
	/**
	 * Displays the user interface in active state
	 * - calls isNotActiveAction as both states have the same output
	 *
	 * @param 	void
	 * @return 	string HTML output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	public function isActiveAction() {
		// in this case we redirect to the "isActive" action as we do not want a different interface when the filter is active
		return $this->doAction('isNotActive');
	}

	
	
	/**
	 * Is not active action
	 *
	 * @param 	void
	 * @return 	string	HTML output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-01
	 */
	public function isNotActiveAction() {
		$view = $this->getView('filter_timeSpan2_userInterface');
		$value = array('from' => $this->fromDate->getHtmlEncodedValue($this->outputFormat), 'to' => $this->toDate->getHtmlEncodedValue($this->outputFormat));
		$view->addItem($value, 'value');
		$view->addItem($this->conf, 'filterconf');
		return $view->render();

	}



	/**
	 * This method will be called to generate the output for the filter breadcrumb.
	 * If you want additional functionality or a different output overwrite this method.
	 *
	 * @param 	void
	 * @return 	string HTML ouput
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-06
	 */
	public function breadcrumbAction() {
		if (!empty($this->value)) {
			// todo ry21 add some localization here!
			$value = 'Zeitspanne: ' . $this->formatTimeSpan();
		} else {
			$value = 'Not set';
		}

		$view = $this->getView('filter_breadcrumb');
		$view->addItem($this->label, 'label');
		$view->addItem($value, 'value');
		return $view->render();
	}
	
	

    /****************************************************************************************************************
     * Template methods
     ****************************************************************************************************************/
    
    /**
     * Pre-Submit action
     *
     * @param   void
     * @return  string  HTML output
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-02-27
     */
    public function preSubmit() {
        // save the incoming parameters as filter-value objects to your value property here
        // validation should be done before, otherwise, this could result in an exception!
        $this->fromDate = tx_ptlist_filterValueDate::getInstanceByDateAndFormat($this->params['from'], $this->inputFormat);
        $this->toDate   = tx_ptlist_filterValueDate::getInstanceByDateAndFormat($this->params['to'], $this->inputFormat);
    }
    
    
    
    /**
     * Validation of submitted values. Checks, whether "from" and "to" values are both set
     *
     * @return bool
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-24
     * 
     */
    public function validate() {
    	if ($this->params['from'] != '' && $this->params['to'] != '') {
    		return true;
    	}
    	return false;
    }
	

	
	/****************************************************************************************************************
     * Domain Logic -
     * implementing abstract methods from parent class
     ****************************************************************************************************************/
	
    /**
     * Get sql where clause snippet
     *
     * @param   array   (optional) if empty the function takes $this->value as  $value
     * @return  string  sql where clause snippet
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-07-17
     */
    public function getSqlWhereClauseSnippet() {
    	if ($this->params['from'] != '' && $this->params['to'] != '') {
            $rangeSnippet = $this->getRangeSnippet();
    	} else {
    		$rangeSnippet = ' 1 ';
    	}
        return $rangeSnippet;
    }

	
	
	/****************************************************************************************************************
     * Helper methods
     ****************************************************************************************************************/

	/**
	 * Format timespan
	 *
	 * @return 	string	formatted timespan string
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-09
	 */
	protected function formatTimeSpan() {
		if ($this->fromDate->getValueByFormat('Y') == $this->toDate->getValueByFormat('Y')) {
			if ($this->fromDate->getValueByFormat('m') == $this->toDate->getValueByFormat('m')) {
				if ($this->fromDate->getValueByFormat('d') == $this->toDate->getValueByFormat('d')) {
					// same (single) day
					$value = $this->fromDate->getValueByFormat($this->outputFormat);
				} else {
					// different day, but same month
					$value = $this->fromDate->getValueByFormat('d') . '-' . $this->toDate->getValueByFormat($this->outputFormat);
				}
			} else {
				// different day and different month, but same year
				$value = $this->fromDate->getValueByFormat('d.m.') . '-' . $this->toDate->getValueByFormat($this->outputFormat);
			}
		} else {
			// completely different
			$value = $this->fromDate->getValueByFormat('d.m.y') . '-' . $this->ToDate->getValueByFormat('d.m.y');
		}
		return $value;
	}



	/**
	 * Get sql where clause snippet
	 *
	 * @return 	string 	sql where clause snippet
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>, Michael Knoll <knoll@punkt.de>
	 * @since	2009-07-17
	 */
	protected function getRangeSnippet() {
        $sqlWhereClauseSnippet = array();
        $snippet = '';
        
		if ($this->dateFieldType == 'date') {
			// Generate where clause for date fields
            $snippet  = $this->getDbColumn() . 
                        ' BETWEEN \'' . 
                        $this->fromDate->getSqlEncodedValue(tx_ptlist_filterValueDate::YYYY_DASH_MM_DASH_DD_OUTPUT_FORMAT) . 
                        '\' AND \'' . 
                        $this->toDate->getSqlEncodedValue(tx_ptlist_filterValueDate::YYYY_DASH_MM_DASH_DD_OUTPUT_FORMAT) .
                        '\''; 
		} elseif($this->dateFieldType == 'timestamp') {
            // Generate where clause for timestamp fields
	        $sqlWhereClauseSnippet[] = $this->getDbColumn() . ' >= ' . $this->fromDate->getValueByFormat('U');
	        $sqlWhereClauseSnippet[] = $this->getDbColumn() . ' <= ' . $this->toDate->getValueByFormat('U');
	        $snippet = implode(' AND ', $sqlWhereClauseSnippet);
		} else {
			// Non-valid datefield type set!
			throw new tx_pttools_exceptionConfiguration("No valid date field type set.", "No valid date field type set for timespan filter. Type was $dateFieldType but can only be 'date' or 'timestamp'!");
		}
        
        return $snippet;
    }

    
    
    /**
     * Returns the DB column for the filter to be applied to
     * 
     * @return string
     * @author Fabrizio Branca <mail@fabriziobranca.de>
     * @since 2009-07-17
     */
    protected function getDbColumn() {
        $table = $this->dataDescriptions->getItemByIndex(0)->get_table();
        $field = $this->dataDescriptions->getItemByIndex(0)->get_field();

        return $table.'.'.$field;
    }

}

?>