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

require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_exception.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_assert.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_debug.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_div.php';

require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_filter.php';
require_once t3lib_extMgm::extPath('pt_list').'view/filter/datePicker/class.tx_ptlist_view_filter_datePicker_userInterface.php';

/**
 * Class implementing a Datepicker filter
 *
 * @author		Joachim Mathes
 * @since		2009-07-14
 * @package     Typo3
 * @subpackage  pt_list
 * @version     $Id$
 */
class tx_ptlist_controller_filter_datePicker extends tx_ptlist_filter {



	/***************************************************************************
     * Modifying standard pt_mvc functionality
     **************************************************************************/

	/**
	 * MVC init method
	 *
	 * @param   void
	 * @return  void
	 * @throws  tx_pttools_exceptionAssertion  if more than one column is attached to the filters columnCollection
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-01-23
	 */
	public function init() {
        $this->requireT3Extension('pt_jqueryui');
		parent::init();
        tx_pttools_assert::isInRange(count($this->dataDescriptions),
                                     1,
                                     2,
                                     array('message' => sprintf('This filter can only be used with 1 or 2 dataDescriptions (dataDescriptions found: "%s"',
                                                                count($this->dataDescriptions))));
	}
	
	
	
	/***************************************************************************
     * Action Methods
     **************************************************************************/

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
	 * Sets various date picker features which are defined in TypoScript
	 * configuration
	 *
	 * @param		void
	 * @return		string	HTML output
	 * @author		Joachim Mathes <mathes@punkt.de>
	 * @since		2009-07-21
	 */
	public function isNotActiveAction() {

		// Get View
		$view = $this->getView('filter_datePicker_userInterface');

		// Get JSON events
		$datesJSON = $this->getJsonEvents();

		// Set date picker mode
		$datePickerMode = $this->conf['datePickerMode'] == '' ? 'inline' : $this->conf['datePickerMode'];

        // Set the changeMonth mode, i.e. if the month can be changed by selecting from a drop-down list
        $changeMonth = $this->conf['changeMonth'] == '' ? 'true' : $this->conf['changeMonth'];

        // Set the changeYear mode, i.e. if the year can be changed by selecting from a drop-down list
        $changeYear = $this->conf['changeYear'] == '' ? 'true' : $this->conf['changeYear'];

        // Set the path to the calendar icon, which is displayed in inline mode
        $buttonImage = $this->conf['buttonImage'] == '' ? t3lib_extMgm::extRelPath('pt_list').'res/javascript/jqueryui/development-bundle/demos/datepicker/images/calendar.gif' : $this->conf['buttonImage'];

		// Set default date
		$defaultDate = $this->value['date'] == '' ? date('Y-m-d') : $this->value['date'];
		$defaultDate = explode('-', $defaultDate);
		$defaultDate[1]--; // peculiar JavaScript date feature
        
		// Set View items for Smarty template
		$view->addItem($this->submitLabel, 'submitLabel');
		$view->addItem($datesJSON, 'datesJSON', false);
		$view->addItem($datePickerMode, 'datePickerMode');
		$view->addItem($changeMonth, 'changeMonth');
		$view->addItem($changeYear, 'changeYear');
		$view->addItem($buttonImage, 'buttonImage');
		$view->addItem($defaultDate, 'defaultDate');

		return $view->render();
	}



    /***************************************************************************
     * Template methods
     **************************************************************************/

	/**
	 * Pre Submit functionality
	 *
	 * @param		void
	 * @return		string	HTML output
	 * @author		Joachim Mathes <mathes@punkt.de>
	 * @since		2009-07-20
	 */
	public function preSubmit() {
		// Save the incoming parameters to derived value property here.
		$this->value = array('date' => $this->params['date']);
	}



    /***************************************************************************
     * Domain logic
     **************************************************************************/

    /**
     * Get SQL where clause snippet
     *
     * This is an inherited abstract function from parent class tx_ptlist_filter.
     * Thus it has to be implemented in this class.
     *
     * @return      string  sql where clause snippet
     * @author      Joachim Mathes <mathes@punkt.de>
     * @since       2009-07-17
     */
    public function getSqlWhereClauseSnippet() {

        $date = $this->value['date'];
        $dbColumn = $this->getDbColumn();

        // Check for correctness of date parameter.
        tx_pttools_assert::isNotEmpty($date, array('message' => 'Value "date" must not be empty but was empty.'));

        // Determine field type of date field (timestamp or date format; default: timestamp).
        // This information has to be given in the TypoScript config property `dateFieldType'.
        $dateFieldType = $this->conf['dateFieldType'] == '' ? 'timestamp' : $this->conf['dateFieldType'];

        $table = $this->dataDescriptions->getItemByIndex(0)->get_table();

        switch ($dateFieldType) {
        case 'date':
            $sqlWhereClauseSnippet = "DATE_FORMAT(" . $dbColumn . ", '%Y-%m-%d') = " . $GLOBALS['TYPO3_DB']->fullQuoteStr($date, $table); // prevents SQL injection!
            break;
        case 'timestamp':
            $sqlWhereClauseSnippet = "FROM_UNIXTIME(" . $dbColumn . ", '%Y-%m-%d') = " . $GLOBALS['TYPO3_DB']->fullQuoteStr($date, $table); // prevents SQL injection!
            break;
        default:
            throw new tx_pttools_exceptionConfiguration("No valid date field type set.",
                                                        "No valid date field type set for datePicker filter. Type was " . $dateFieldType . " but can only be 'date' or 'timestamp'!");
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
		$table = $this->dataDescriptions->getItemByIndex(0)->get_table();

		switch ($dateFieldType) {
		case 'date':
			$select = 'DISTINCT DATE_FORMAT(' . $table . '.' . $field . ", '%e') AS day,
			                    DATE_FORMAT(" . $table . '.' . $field . ", '%c') AS month,
			                    DATE_FORMAT(" . $table . '.' . $field . ", '%Y') AS year";
			break;
		case 'timestamp':
			$select = "DISTINCT FROM_UNIXTIME(" . $table . '.' . $field . ", '%e') AS day,
			                    FROM_UNIXTIME(" . $table . '.' . $field . ", '%c') AS month,
			                    FROM_UNIXTIME(" . $table . '.' . $field . ", '%Y') AS year";
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
                    'listObj' => $listObject,
                    'filterIdentifier' => $this->get_filterIdentifier()
                );
                $where .= t3lib_div::callUserFunction($funcName, $params, $this, '');
                if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Processing hook "%s" for "getEventDates_whereClauseHook" of filter_datePicker', $funcName), $this->extKey, 1, array('params' => $params));
            }
        }

		$data = $listObject->getGroupData($select, $where, $groupBy, $orderBy, $limit, $ignoredFiltersForWhereClause);

		return $data;
	}



	/***************************************************************************
     * Helper methods
     **************************************************************************/

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
	 * Returns an JSON style formatted string of events
	 *
	 * @param      void
	 * @return     unknown
	 * @author     Michael Knoll <knoll@punkt.de>, Joachim Mathes <mathes@punkt.de>
	 * @since      2009-09-24
	 */
	protected function getJsonEvents() {
		// Get event dates
        $dates = $this->getEventDates();
        // Create JSON object as plain string
        $datesArray = array();
        foreach ($dates as $key => $value) {
            $datesArray[$key] = sprintf("{'year':%s, 'month':%s, 'day':%s}",
                                       $value['year'], $value['month'], $value['day']);
        }
        $datesJSON = "{'dates':[".implode(',', $datesArray)."]}";
        return $datesJSON;
	}

    /**
     * require T3 extension
     * @param   string  $extensionKey  extension key
     * @return  void
     * @author  Joachim Mathes <mathes@punkt.de>
     * @sincs   2009-11-13
     */
    protected function requireT3Extension($extensionKey) {
        if (!t3lib_extMgm::isLoaded('pt_jqueryui')) {
			throw new tx_pttools_exception('You need to install and load pt_jqueryui to run datepicker filter!');
		}
    }

}
?>