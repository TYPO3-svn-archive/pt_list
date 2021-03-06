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

require_once t3lib_extMgm::extPath('pt_list').'classes/class.tx_ptlist_dateX.php';
require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_filter.php';
require_once t3lib_extMgm::extPath('pt_list').'view/filter/datePager/class.tx_ptlist_view_filter_datePager_userInterface.php';

/**
 * Date Pager filter
 *
 * @author      Joachim Mathes
 * @since       2009-09-08
 * @package     TYPO3
 * @subpackage  pt_list\controller\filter
 * @version     $Id$
 */
class tx_ptlist_controller_filter_datePager extends tx_ptlist_filter {
	protected $value = array();
	protected $smartyTemplateVariables = array();
	const FIRSTDAYOFWEEK = 1; // sets index to monday, which is the first day of the week according to DIN 1355

	/**
	 * MVC init method
	 *
	 * @param   void
	 * @return  void
	 * @throws  tx_pttools_exceptionAssertion if less than one or more than two columns are attached to the filters columnCollection
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-09-08
	 */
	public function init() {

		//init dateX and store today in session
		tx_ptlist_dateX::getInstance();

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
	 * @since   2009-09-08
	 */
	public function isActiveAction() {
		return $this->doAction('isNotActive');
	}

	/**
	 * 'Is not active'-action
	 *
	 * @param   void
	 * @return  string   HTML output for Smarty template
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-09-08
	 */
	public function isNotActiveAction() {
		return $this->renderView();
	}

	/**
	 * Validate
	 *
	 * @param   void
	 * @return  bool
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-09-08
	 */
	public function validate() {
		return true;
	}

	/**
	 * Submit action
	 *
	 * @param   void
	 * @return  string	HTML output
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-09-08
	 */
	public function submitAction() {
		switch ($this->params['mode']) {
			case 'next':
				$this->incrementPagerValues();
				break;
			case 'prev':
				$this->decrementPagerValues();
				break;
			default:
				throw new tx_pttools_exceptionConfiguration("No valid 'mode' set in GET parameters.");
		}
		return parent::submitAction();
	}

	/**
	 * Resets this filter.
	 *
	 * @param   void
	 * @return  void
	 * @author  Simon Schaufelberger <schaufelberger@punkt.de>
	 * @since   2010-08-25
	 */
	public function reset() {
		tx_ptlist_dateX::getInstance()->resetDateX();
		parent::reset();
	}

	/**
	 * Get SQL where clause snippet
	 *
	 * @param   void
	 * @return  string	SQL where clause snippet
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-09-08
	 */
	public function getSqlWhereClauseSnippet() {
		$sqlWhereClauseSnippet = '';
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
		return $sqlWhereClauseSnippet;
	}

	/**
	 * Get point of time SQL where clause snippet
	 *
	 * @param   void
	 * @return  string   point of time SQL
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-09
	 */
	protected function getPointOfTimeSqlWhereClauseSnippet() {

		$dateX = tx_ptlist_dateX::getInstance();
		$date = $dateX->getDateX();
		$dateTimestamp = $dateX->getDateXAsTimestamp();

		$startDateColumn = $this->dataDescriptions->getItemByIndex(0)->get_table()
		. '.'
		. $this->dataDescriptions->getItemByIndex(0)->get_field();
		$sqlDateFunction = $this->determineSqlDateFunction();
		$dateEntity = $this->determineDateEntity();
		switch ($dateEntity) {
			case 'day':
				$sqlWhereClauseSnippet = $sqlDateFunction . "(" . $startDateColumn . ", '%Y-%m-%d') = '" . $date . "'";
				break;
			case 'week':
				$sqlWhereClauseSnippet = "WEEKOFYEAR(" . $sqlDateFunction . "(" . $startDateColumn . ", '%Y-%m-%d')) = '" . date('W', $dateTimestamp) . "' AND " . $sqlDateFunction . "(" . $startDateColumn . ", '%Y') = '" . date('Y', $dateTimestamp) . "'";
				break;
			case 'month':
				$sqlWhereClauseSnippet = $sqlDateFunction . "(" . $startDateColumn . ", '%Y-%m') = '" . date('Y-m', $dateTimestamp) . "'";
				break;
			case 'year':
				$sqlWhereClauseSnippet =  $sqlDateFunction . "(" . $startDateColumn . ", '%Y') = '" . date('Y', $dateTimestamp) . "'";
				break;
			default:
				throw new tx_pttools_exceptionConfiguration("No valid 'dateEntity' set in Typoscript configuration.");
		}
		return $sqlWhereClauseSnippet;
	}

	/**
	 * Get period of time SQL where clause snippet
	 *
	 * @param   void
	 * @return  string   period of time SQL
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-09
	 */
	protected function getPeriodOfTimeSqlWhereClauseSnippet() {

		$dateX = tx_ptlist_dateX::getInstance();
		$date = $dateX->getDateX();
		$dateTimestamp = $dateX->getDateXAsTimestamp();

		$startDateColumn = $this->dataDescriptions->getItemByIndex(0)->get_table()
		. '.'
		. $this->dataDescriptions->getItemByIndex(0)->get_field();
		$endDateColumn = $this->dataDescriptions->getItemByIndex(1)->get_table()
		. '.'
		. $this->dataDescriptions->getItemByIndex(1)->get_field();
		$sqlDateFunction = $this->determineSqlDateFunction();
		$dateEntity = $this->determineDateEntity();

		$entityAdjustment = intval($this->value['value']);

		switch ($dateEntity) {
			case 'day':
				$start = "STR_TO_DATE(" . $sqlDateFunction . "(" . $startDateColumn . ", '%Y-%m-%d'), '%Y-%m-%d')";
				$end =   "STR_TO_DATE(" . $sqlDateFunction . "(" . $endDateColumn .   ", '%Y-%m-%d'), '%Y-%m-%d')";

				$adjustmentDate = "STR_TO_DATE('" . $date . "', '%Y-%m-%d')";

				$sqlWhereClauseSnippet = $start . ' <= ' . $adjustmentDate . ' AND ' . $end . ' >= ' . $adjustmentDate;
				break;
			case 'week':
				$sqlWhereClauseSnippet =
					"WEEKOFYEAR(" . $sqlDateFunction . "(" . $startDateColumn . ", '%Y-%m-%d')) <= '" . date('W', $dateTimestamp) . "' AND " . $sqlDateFunction . "(" . $startDateColumn . ", '%Y') <= '" . date('Y', $dateTimestamp) . "' "
					. "AND "
					. "WEEKOFYEAR(" . $sqlDateFunction . "(" . $endDateColumn . ", '%Y-%m-%d')) >= '" . date('W', $dateTimestamp) . "' AND " . $sqlDateFunction . "(" . $endDateColumn . ", '%Y') >= '" . date('Y', $dateTimestamp) . "'";
					break;
			case 'month':
				$sqlWhereClauseSnippet =
					"STR_TO_DATE(" . $sqlDateFunction . "(" . $startDateColumn . ", '%Y-%m'), '%Y-%m') <= STR_TO_DATE('" . date('Y-m', $dateTimestamp) . "', '%Y-%m') "
					. "AND "
					. "STR_TO_DATE(" . $sqlDateFunction . "(" . $endDateColumn . ", '%Y-%m'), '%Y-%m') >= STR_TO_DATE('" . date('Y-m', $dateTimestamp) . "', '%Y-%m')";

					break;
			case 'year':
				$sqlWhereClauseSnippet =
					"STR_TO_DATE(" . $sqlDateFunction . "(" . $startDateColumn . ", '%Y'), '%Y') <= STR_TO_DATE('" . date('Y', $dateTimestamp) . "', '%Y') "
					. "AND "
					. "STR_TO_DATE(" . $sqlDateFunction . "(" . $endDateColumn . ", '%Y'), '%Y') >= STR_TO_DATE('" . date('Y', $dateTimestamp) . "', '%Y')";
					break;
			default:
				throw new tx_pttools_exceptionConfiguration("No valid 'dateEntity' set in Typoscript configuration.");
		}
		return $sqlWhereClauseSnippet;
	}

	/**
	 * Renders the header text for the filter
	 *
	 * The TS variables {field:begin} and {field:end} are replaced by their
	 * respective values depending on the chosen date entity.
	 *
	 * @param   void
	 * @return  string	rendered header
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-09-15
	 */
	protected function renderHeader() {

		$dateX = tx_ptlist_dateX::getInstance();
		$date = $dateX->getDateX();

		//Get configuration from TypoScript
		$entity = $this->determineDateEntity();
		$beginFormat = $this->conf['beginFormat'] == '' ? '%Y-%m-%d' : $this->conf['beginFormat'];
		$endFormat = $this->conf['endFormat'] == '' ? '%Y-%m-%d' : $this->conf['endFormat'];

		$firstDayOfWeek = $this->conf['firstDayOfWeek'] == '' ? self::FIRSTDAYOFWEEK : intval($this->conf['firstDayOfWeek']);

		switch ($entity) {
			case 'day':
				$dateEntityBegin = strftime($beginFormat, strtotime($date));
				$dateEntityEnd = strftime($endFormat, strtotime($date));
				break;
			case 'week':
				$dateEntityBegin = strftime($beginFormat, strtotime($dateX->getFirstDayOfWeek($firstDayOfWeek)));
				$dateEntityEnd = strftime($endFormat, strtotime($dateX->getLastDayOfWeek($firstDayOfWeek)));
				break;
			case 'month':
				$dateEntityBegin = strftime($beginFormat, strtotime($dateX->getFirstDayOfMonth()));
				$dateEntityEnd = strftime($endFormat, strtotime($dateX->getLastDayOfMonth()));
				break;
			case 'year':
				$dateEntityBegin = strftime($beginFormat, strtotime($dateX->getFirstDayOfYear()));
				$dateEntityEnd = strftime($endFormat, strtotime($dateX->getLastDayOfYear()));
				break;
			default:
				throw new tx_pttools_exceptionConfiguration("No valid 'entity' set in Typoscript configuration.");
		}

		$renderValues = array('begin' => $dateEntityBegin, 'end' => $dateEntityEnd);

		// Rendering configuration
		if (isset($this->conf['header']) && isset($this->conf['header.'])) {
			$renderConfig['renderObj'] = $this->conf['header'];
			$renderConfig['renderObj.'] = $this->conf['header.'];
		}
		if (isset($this->conf['renderUserFunctions.'])) {
			$renderConfig['renderUserFunctions.'] = $this->conf['renderUserFunctions.'];
		}

		if (isset($renderConfig)) {
			$header = tx_ptlist_div::renderValues($renderValues, $renderConfig);
		}
		else {
			$header = '';
		}
		return $header;
	}

	/**
	 * Render view
	 *
	 * @param   void
	 * @return  string  HTML output
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-09
	 */
	protected function renderView() {
		$this->defineSmartyTemplateVariables();
		$view = $this->getView('filter_datePager_userInterface');
		$view->addItem($this->smartyTemplateVariables, 'span');
		return $view->render();
	}

	/**
	 * Define Smarty template variables
	 *
	 * @param   void
	 * @return  void
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-09
	 */
	protected function defineSmartyTemplateVariables() {
		$this->smartyTemplateVariables['entity'] = $this->determineDateEntity();
		$this->smartyTemplateVariables['prevValue'] = $this->conf['prevValue'] == '' ? -1 : intval($this->conf['prevValue']);
		$this->smartyTemplateVariables['nextValue'] = $this->conf['nextValue'] == '' ? 1 : intval($this->conf['nextValue']);
		$this->smartyTemplateVariables['labelPrevious'] = $this->conf['labelPrevious'] == '' ? '<' : $GLOBALS['TSFE']->cObj->stdWrap($this->conf['labelPrevious'],  $this->conf['labelPrevious.']);
		$this->smartyTemplateVariables['labelNext'] = $this->conf['labelNext'] == '' ? '>' : $GLOBALS['TSFE']->cObj->stdWrap($this->conf['labelNext'],  $this->conf['labelNext.']);
		$this->smartyTemplateVariables['header'] = $this->renderHeader();
	}

	/**
	 * Increment pager values
	 *
	 * @param   void
	 * @return  void
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-09
	 */
	protected function incrementPagerValues() {
		$dateX = tx_ptlist_dateX::getInstance();
		$quantity = $this->conf['nextValue'] == '' ? 1 : intval($this->conf['nextValue']);
		$dateX->incrementDateXByEntity($quantity, $this->determineDateEntity());
	}

	/**
	 * Decrement pager values
	 *
	 * @param   void
	 * @return  void
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-09
	 */
	protected function decrementPagerValues() {
		$dateX = tx_ptlist_dateX::getInstance();
		$quantity = $this->conf['prevValue'] == '' ? 1 : abs( intval($this->conf['prevValue']) );
		$dateX->decrementDateXByEntity($quantity, $this->determineDateEntity());
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
				throw new tx_pttools_exceptionConfiguration("No valid 'dateFieldType' set in Typoscript configuration.");
		}
		return $sqlDateFunction;
	}

	/**
	 * Determine date entity
	 *
	 * @param   void
	 * @return  string  SQL date function
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-11-09
	 */
	protected function determineDateEntity() {
		return $this->conf['entity'] == '' ? 'day' : $this->conf['entity'];
	}
}