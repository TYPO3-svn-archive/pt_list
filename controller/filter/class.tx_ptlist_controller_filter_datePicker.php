<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Joachim Mathes <mathes@punkt.de>
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
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_filter.php';
require_once t3lib_extMgm::extPath('pt_list').'view/filter/datePicker/class.tx_ptlist_view_filter_datePicker_userInterface.php';

/**
 * Class implementing a Datepicker filter
 *
 * @version		$Id: class.tx_ptlist_controller_filter_timeSpan.php 20112 2009-05-09 11:57:47Z fabriziobranca $
 * @author		Joachim Mathes
 * @since		2009-07-14
 */
class tx_ptlist_controller_filter_datePicker extends tx_ptlist_filter {

	/**
	 * MVC init method
	 * 
	 * Checks if the column collection contains exactly one column as this filter
	 * can be used only with one column at the same time.
	 *
	 * @param		void
	 * @return		void
	 * @throws		tx_pttools_exceptionAssertion	if more than one column is attached to the filters columnCollection
	 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since		2009-01-23
	 */
	public function init() {
		parent::init();
		tx_pttools_assert::isEqual(count($this->dataDescriptions),
				   1,
				   array('message' => sprintf('This filter can only be used with 1 dataDescription (dataDescription found: "%s"',
								  count($this->dataDescriptions))));
	}



	/**
	 * Displays the user interface in active state
	 *
	 * This function is called by defaultAction() from parent class tx_ptlist_filter.
	 * As a pt_mvc derived controller tx_ptlist_filter calls defaultAction(), if no action parameter is specified.
	 *
	 * Calls isNotActiveAction().
	 *
	 * @param		void
	 * @return		string HTML output
	 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since		2009-01-19
	 */
	public function isActiveAction() {
		// In this case we redirect to the "isActive" action as we do not want a different interface when the filter is active
		return $this->doAction('isNotActive');
	}



	/**
	 * Is not active action
	 *
	 * @param		void
	 * @return		string	HTML output
	 * @author		Joachim Mathes <mathes@punkt.de>
	 * @since		2009-07-21
	 */
	public function isNotActiveAction() {

		// Get View
		$view = $this->getView('filter_datePicker_userInterface');

		// Get event dates
		$dates = $this->getEventDates();
		// Create JSON object as plain string
		$datesArray = array();
		foreach ($dates as $key => $value) {
			$datesArray[$key] = sprintf("{'year':%s, 'month':%s, 'day':%s}",
									   $value['year'], $value['month'], $value['day']);
		}
		$datesJSON = "{'dates':[".implode(',', $datesArray)."]}";

		// Get date picker mode
		$datePickerMode = $this->conf['datePickerMode'] == '' ? 'inline' : $this->conf['datePickerMode'];

		// Set default date
		$defaultDate = $this->value['date'] == '' ? date('Y-m-d') : $this->value['date'];
		$defaultDate = explode('-', $defaultDate);
		$defaultDate[1]--; // peculiar JavaScript date feature

		// Set View items for Smarty template
		$view->addItem($this->submitLabel, 'submitLabel');
		$view->addItem($datesJSON, 'datesJSON', false);
		$view->addItem($datePickerMode, 'datePickerMode');
		$view->addItem($defaultDate, 'defaultDate');

		return $view->render();
	}



	/**
	 * Validate function
	 *
	 * This function is called by the parent::submitAction() method.
	 *
	 * @param		void
	 * @return		bool
	 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since		2009-02-27
	 */
	public function validate() {
		return true;
	}



	/**
	 * Submit action
	 *
	 * @param		void
	 * @return		string	HTML output
	 * @author		Joachim Mathes <mathes@punkt.de>
	 * @since		2009-07-20
	 */
	public function submitAction() {

		// Save the incoming parameters to derived value property here.
		$this->value = array('date' => $this->params['date']);

		// Let the parent action do the submission.
		// It calls the validate() function.
		return parent::submitAction();
	}


	/**
	 * Returns array of event dates
	 *
	 * @return	  array			 event dates
	 * @author	  Joachim Mathes <mathes@punkt.de>
	 * @since	  2009-07-20
	 */
	protected function getEventDates() {
		// Retrieve list object from registry
		$listObject = tx_pttools_registry::getInstance()->get($this->listIdentifier.'_listObject'); /* @var $listObject tx_ptlist_list */

		// Prepare parameters for the getGroupData() call

		// SELECT
		// Determine field type of date field (timestamp or date format; default: timestamp).
		// This information has to be given in the TypoScript config property `dateFieldType'.
		$dateFieldType = $this->conf['dateFieldType'] == '' ? 'timestamp' : $this->conf['dateFieldType'];
		$field = $this->dataDescriptions->getItemByIndex(0)->get_field();

		switch ($dateFieldType) {
		case 'date':
			$select = "DISTINCT DATE_FORMAT($field, '%e') AS day, DATE_FORMAT($field, '%c') AS month, DATE_FORMAT($field, '%Y') AS year";
			break;
		case 'timestamp':
			$select = "DISTINCT FROM_UNIXTIME($field, '%e') AS day, FROM_UNIXTIME($field, '%c') AS month, FROM_UNIXTIME($field, '%Y') AS year";
			break;
		default:
			throw new tx_pttools_exceptionConfiguration("No valid date field type set.",
														"No valid date field type set for eventCalender filter. Type was $dateFieldType but can only be 'date' or 'timestamp'!");
		}

		// WHERE
		$where = '';
		// GROUP BY
		$groupBy = '';
		// ORDER BY
		$orderBy = '';
		// LIMIT
		$limit = '';
		// Ignore all filters
		$ignoredFiltersForWhereClause = '__ALL__';
		
        
        // HOOK: allow multiple hooks to append individual additional where clause conditions (added by rk 19.08.09)
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['filter_datePicker']['getEventDates_whereClauseHook'])) {
            foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['filter_datePicker']['getEventDates_whereClauseHook'] as $funcName) {
                $params = array(
                    'where' => $where,
                );
                $where .= t3lib_div::callUserFunction($funcName, $params, $this, '');
                if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Processing hook "%s" for "getEventDates_whereClauseHook" of filter_datePicker', $funcName), $this->extKey, 1, array('params' => $params));
            }
        }   
        

		$data = $listObject->getGroupData($select, $where, $groupBy, $orderBy, $limit, $ignoredFiltersForWhereClause);

		return $data;
	}


	/**
	 * Get database column
	 *
	 * @return		string	sql database field
	 * @author		Joachim Mathes <mathes@punkt.de>
	 * @since		2009-07-17
	 */
	protected function getDbColumn() {
		$table = $this->dataDescriptions->getItemByIndex(0)->get_table();
		$field = $this->dataDescriptions->getItemByIndex(0)->get_field();

		return $table.'.'.$field;
	}



	/**
	 * Get SQL where clause snippet
	 *
	 * This is an inherited abstract function from parent class tx_ptlist_filter.
	 * Thus it has to be implemented in this class.
	 *
	 * @return		string	sql where clause snippet
	 * @author		Joachim Mathes <mathes@punkt.de>
	 * @since		2009-07-17
	 */
	public function getSqlWhereClauseSnippet() {

		$date = $this->value['date'];
		$dbColumn = $this->getDbColumn();

		// Check for correctness of date parameter.
		tx_pttools_assert::isNotEmpty($date, array('message' => 'Value "date" must not be empty but was empty.'));

		// Determine field type of date field (timestamp or date format; default: timestamp).
		// This information has to be given in the TypoScript config property `dateFieldType'.
		$dateFieldType = $this->conf['dateFieldType'] == '' ? 'timestamp' : $this->conf['dateFieldType'];

		switch ($dateFieldType) {
		case 'date':
			$sqlWhereClauseSnippet = "DATE_FORMAT(".$dbColumn.", '%Y-%m-%d') = '".$date."'";
			break;
		case 'timestamp':
			$sqlWhereClauseSnippet = "FROM_UNIXTIME(".$dbColumn.", '%Y-%m-%d') = '".$date."'";
			break;
		default:
			throw new tx_pttools_exceptionConfiguration("No valid date field type set.",
														"No valid date field type set for eventCalender filter. Type was ".$dateFieldType." but can only be 'date' or 'timestamp'!");
		}
        
        
        // HOOK: allow multiple hooks to append individual additional where clause conditions (added by rk 19.08.09)
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['filter_datePicker']['getSqlWhereClauseSnippetHook'])) {
            foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['filter_datePicker']['getSqlWhereClauseSnippetHook'] as $funcName) {
                $params = array(
                    'sqlWhereClauseSnippet' => $sqlWhereClauseSnippet,
                );
                $sqlWhereClauseSnippet .= t3lib_div::callUserFunction($funcName, $params, $this, '');
                if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Processing hook "%s" for "getSqlWhereClauseSnippetHook" of filter_datePicker', $funcName), $this->extKey, 1, array('params' => $params));
            }
        }   
        

		return $sqlWhereClauseSnippet;
		
	}
}
?>