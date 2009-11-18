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
 * TODO: The date logic might better be encapsulated into separate classes.
 *       Therefore filter design has to be discussed with respect to spreading
 *       filter code over several classes.
 * TODO: Implement Ajax functionality, since the given approach will not scale
 *       for a huge amount of event dates.
 *
 * @author		Joachim Mathes
 * @since		2009-07-14
 * @package     Typo3
 * @subpackage  pt_list
 * @version     $Id$
 */
class tx_ptlist_controller_filter_datePicker extends tx_ptlist_filter {
    protected $smartyTemplateVariables = array();

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
                                     array('message' => sprintf('This filter can only be used with 1 or 2 dataDescriptions (dataDescriptions found: "%s")',
                                                                count($this->dataDescriptions))));
	}

	/**
	 * 'Is active'-action
	 *
	 * @param   void
	 * @return  string HTML output
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-13
	 */
	public function isActiveAction() {
		return $this->doAction('isNotActive');
	}

	/**
	 * 'Is not active'-action
	 *
	 * @param   void
	 * @return  string	HTML output
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-07-21
	 */
	public function isNotActiveAction() {
        return $this->renderView();
    }

	/**
	 * Submit action
	 *
	 * @param   void
	 * @return  string	HTML output
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-07-20
	 */
	public function submitAction() {
		$this->value = array('date' => $this->params['date']);
        return parent::submitAction();
	}

    /**
     * Get SQL where clause snippet
     *
     * @param   void
     * @return  string  SQL where clause snippet
     * @author  Joachim Mathes <mathes@punkt.de>
     * @since   2009-07-17
     */
    public function getSqlWhereClauseSnippet() {
        switch(count($this->dataDescriptions)) {
            case 1:
                $sqlWhereClauseSnippet = $this->getPointOfTimeSqlWhereClauseSnippet();
                break;
            case 2:
                $sqlWhereClauseSnippet = $this->getPeriodOfTimeSqlWhereClauseSnippet();
                break;
            default:
                throw new tx_pttools_exceptionConfiguration("No valid number of data descriptions.");
        }
        $this->getSqlWhereClauseSnippetHook($sqlWhereClauseSnippet);
        return $sqlWhereClauseSnippet;
    }

    /**
	 * Get point of time SQL where clause snippet
	 *
	 * @param   void
	 * @return  string   point of time SQL where clause snippet
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-13
	 */
    protected function getPointOfTimeSqlWhereClauseSnippet() {
        $cachedDate = $this->value['date'];
        tx_pttools_assert::isNotEmpty($cachedDate, array('message' => 'Value "date" must not be empty but was empty.'));

        $tableName = $this->getTableName();
        $startDateColumn = $this->getDateColumnByIndexNumber(0);
        $sqlDateFunction = $this->determineSqlDateFunction();
        $sqlWhereClauseSnippet = $sqlDateFunction . "(" . $startDateColumn . ", '%Y-%m-%d') = " . $GLOBALS['TYPO3_DB']->fullQuoteStr($cachedDate, $tableName);
        return $sqlWhereClauseSnippet;
    }

    /**
	 * Get period of time SQL where clause snippet
	 *
	 * @param   void
	 * @return  string   period of time SQL where clause snippet
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-13
	 */
    protected function getPeriodOfTimeSqlWhereClauseSnippet() {
        $cachedDate = $this->value['date'];
        tx_pttools_assert::isNotEmpty($cachedDate, array('message' => 'Value "date" must not be empty but was empty.'));

        $tableName = $this->getTableName();
        $startDateColumn = $this->getDateColumnByIndexNumber(0);
        $endDateColumn = $this->getDateColumnByIndexNumber(1);
        $sqlDateFunction = $this->determineSqlDateFunction();
        $sqlWhereClauseSnippet = $sqlDateFunction . "(" . $startDateColumn . ", '%Y-%m-%d') <= " . $GLOBALS['TYPO3_DB']->fullQuoteStr($cachedDate, $tableName)
                                 . " AND "
                                 . $sqlDateFunction . "(" . $endDateColumn . ", '%Y-%m-%d') >= " . $GLOBALS['TYPO3_DB']->fullQuoteStr($cachedDate, $tableName);
        return $sqlWhereClauseSnippet;
    }

    /**
     * Render view
     *
	 * @param   void
	 * @return  unknown
     * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-09-24
	 */
    protected function renderView () {
        $this->defineSmartyTemplateVariables();
        $view = $this->getView('filter_datePicker_userInterface');
        $view->addItem($this->submitLabel, 'submitLabel');
		$view->addItem($this->smartyTemplateVariables['datesJSON'], 'datesJSON', false);
		$view->addItem($this->smartyTemplateVariables['datePickerMode'], 'datePickerMode');
		$view->addItem($this->smartyTemplateVariables['changeMonth'], 'changeMonth');
		$view->addItem($this->smartyTemplateVariables['changeYear'], 'changeYear');
		$view->addItem($this->smartyTemplateVariables['buttonImage'], 'buttonImage');
		$view->addItem($this->smartyTemplateVariables['defaultDate'], 'defaultDate');
        return $view->render();
    }

    /**
	 * Define Smarty template variables
	 *
	 * @param   void
	 * @return  void
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-13
	 */
    protected function defineSmartyTemplateVariables() {
        $this->smartyTemplateVariables['datesJSON'] = $this->getEventDates();
        $this->smartyTemplateVariables['datePickerMode'] = $this->conf['datePickerMode'] == '' ? 'inline' : $this->conf['datePickerMode'];
        $this->smartyTemplateVariables['changeMonth'] = $this->conf['changeMonth'] == '' ? 'true' : $this->conf['changeMonth'];
        $this->smartyTemplateVariables['changeYear'] = $this->conf['changeYear'] == '' ? 'true' : $this->conf['changeYear'];
        $this->smartyTemplateVariables['buttonImage'] = $this->conf['buttonImage'] == '' ? t3lib_extMgm::extRelPath('pt_list').'res/javascript/jqueryui/development-bundle/demos/datepicker/images/calendar.gif' : $this->conf['buttonImage'];
        $this->smartyTemplateVariables['defaultDate'] = $this->value['date'] == '' ? date('Y-m-d') : $this->value['date'];
        $this->smartyTemplateVariables['defaultDate'] = explode('-', $this->smartyTemplateVariables['defaultDate']);
        $this->smartyTemplateVariables['defaultDate'][1]--; // peculiar JavaScript date feature
    }

    /**
	 * Returns an JSON style formatted string of events
	 *
	 * @param   void
	 * @return  unknown
	 * @author  Michael Knoll <knoll@punkt.de>
     * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-09-24
	 */
	protected function getEventDates() {
        switch(count($this->dataDescriptions)) {
            case 1:
                $dates = $this->getPointOfTimeEventDates();
                break;
            case 2:
                $dates = $this->getPeriodOfTimeEventDates();
                break;
            default:
                throw new tx_pttools_exceptionConfiguration("No valid number of data descriptions.");
        }
        return $dates;
	}

    /**
	 * Get point of time event dates
	 *
	 * @param   void
	 * @return  string  JSON formatted string
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-09
	 */
    protected function getPointOfTimeEventDates() {
        $dates = $this->execPointOfTimeEventDatesSql();
        foreach ($dates as $key => $value) {
            $datesArray[$key] = sprintf("{'year':%s, 'month':%s, 'day':%s}",
                                        $value['year'], $value['month'], $value['day']);
        }
        $datesInJsonFormat = "{'dates':[".implode(',', $datesArray)."]}";
        return $datesInJsonFormat;
    }

    /**
	 * Get period of time event dates
	 *
	 * @param   void
	 * @return  string  JSON formatted string
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-09
	 */
    protected function getPeriodOfTimeEventDates() {
        $dates = $this->execPeriodOfTimeEventDatesSql();
        foreach ($dates as $key => $date) {
            $datesArray[$key] = $this->evaluateDatePeriod($date);
        }
        $datesInJsonFormat = "{'dates':[" . implode(',', $datesArray) . " ]}";
        return $datesInJsonFormat;
    }

    /**
	 * Evaluate date period
	 *
	 * @param   array  date array
	 * @return  array  array of JSON formatted strings
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-13
	 */
    protected function evaluateDatePeriod($date) {
        $this->validateDatePeriod($date['period']);
        for ($i = 0; $i <= $date['period']; $i++) {
            $datesInJsonFormat[$i] = sprintf("{'year':%s, 'month':%s, 'day':%s}",
                date('Y', mktime(0, 0, 0, $date['month'], $date['day'] + $i, $date['year'])),
                date('n', mktime(0, 0, 0, $date['month'], $date['day'] + $i, $date['year'])),
                date('j', mktime(0, 0, 0, $date['month'], $date['day'] + $i, $date['year'])));
        }
        return implode(',', $datesInJsonFormat);
    }

    /**
	 * Validate date period
	 *
	 * @param   int    date period
	 * @return  void
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-13
	 */
    protected function validateDatePeriod($datePeriod) {
        if ($datePeriod < 0) {
            throw new tx_pttools_exceptionConfiguration("No valid date period. Enddate is greater than startdate.");
        }
    }

    /**
	 * Exec point of time event dates SQL
	 *
     * @param   void
	 * @return  array  event dates
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-07-20
	 */
	protected function execPointOfTimeEventDatesSql() {
		$listObject = tx_pttools_registry::getInstance()->get($this->listIdentifier.'_listObject');
        $startDateColumn = $this->getDateColumnByIndexNumber(0);
		$sqlDateFunction = $this->determineSqlDateFunction();
        $select = "DISTINCT " . $sqlDateFunction . "(" . $startDateColumn . ", '%e') AS day, "
                              . $sqlDateFunction . "(" . $startDateColumn . ", '%c') AS month, "
                              . $sqlDateFunction . "(" . $startDateColumn . ", '%Y') AS year";
		$where = '';
		$groupBy = '';
		$orderBy = '';
		$limit = '';
		$ignoredFiltersForWhereClause = '__ALL__';

        $this->getEventDatesWhereClauseHook($where, $listObject);

		$dates = $listObject->getGroupData($select, $where, $groupBy, $orderBy, $limit, $ignoredFiltersForWhereClause);

		return $dates;
	}

    /**
	 * Exec period of time event dates SQL
	 *
     * @param   void
	 * @return  array  event dates
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-07-20
	 */
	protected function execPeriodOfTimeEventDatesSql() {
		$listObject = tx_pttools_registry::getInstance()->get($this->listIdentifier . '_listObject');
        $startDateColumn = $this->getDateColumnByIndexNumber(0);
        $endDateColumn = $this->getDateColumnByIndexNumber(1);
        $sqlDateFunction = $this->determineSqlDateFunction();
        $select = "DISTINCT " . $sqlDateFunction . "(" . $startDateColumn . ", '%e') AS day, "
                              . $sqlDateFunction . "(" . $startDateColumn . ", '%c') AS month, "
                              . $sqlDateFunction . "(" . $startDateColumn . ", '%Y') AS year, DATEDIFF(" . $sqlDateFunction . "(" . $endDateColumn . "), " . $sqlDateFunction . "(" . $startDateColumn . ")) AS period";
		$where = '';
		$groupBy = '';
		$orderBy = '';
		$limit = '';
		$ignoredFiltersForWhereClause = '__ALL__';

        $this->getEventDatesWhereClauseHook($where, $listObject);

		$dates = $listObject->getGroupData($select, $where, $groupBy, $orderBy, $limit, $ignoredFiltersForWhereClause);

		return $dates;
    }

    /**
	 * Determine SQL date function
	 *
	 * @param   void
	 * @return  string  SQL date function
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-09
	 */
    protected function determineSqlDateFunction() {
        switch ($this->conf['dateFieldType']) {
        case 'date':
            $sqlDateFunction = 'DATE_FORMAT';
            break;
        case 'timestamp':
            $sqlDateFunction = 'FROM_UNIXTIME';
            break;
		default:
            $sqlDateFunction = 'FROM_UNIXTIME';
            break;
        }
        return $sqlDateFunction;
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

    /**
	 * Get date column by index number
	 *
	 * @param   int     $indexNumber  index number
	 * @return  string  concatenated table and column name
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-13
	 */
    protected function getDateColumnByIndexNumber($indexNumber) {
        return $this->dataDescriptions->getItemByIndex($indexNumber)->get_table()
               . '.'
               . $this->dataDescriptions->getItemByIndex($indexNumber)->get_field();
    }

    /**
	 * Get table name
	 *
	 * @param   void
	 * @return  string  table name
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-13
	 */
    protected function getTableName() {
        return $this->dataDescriptions->getItemByIndex($indexNumber)->get_table();
    }

    /**
     * Hook: getEventDatesWhereClause
     *
     * @param   string  $where       where clause
     * @param   object  $listObject  list object
	 * @return  void
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-13
     */
    protected function getEventDatesWhereClauseHook(&$where, $listObject) {
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
    }

    /**
     * Hook: getSqlWhereClauseSnippetHook
     *
     * @param   string  $sqlWhereClauseSnippet  SQL where clause snippet
	 * @return  void
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-13
     */
    protected function getSqlWhereClauseSnippetHook(&$sqlWhereClauseSnippet) {
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
    }

}
?>