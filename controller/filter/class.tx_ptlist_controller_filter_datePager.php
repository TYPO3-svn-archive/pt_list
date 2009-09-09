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
require_once t3lib_extMgm::extPath('pt_list').'view/filter/datePager/class.tx_ptlist_view_filter_datePager_userInterface.php';

/**
 * Class implementing a Date Pager filter
 *
 * @version  $Id$
 * @author   Joachim Mathes
 * @since    2009-09-08
 */
class tx_ptlist_controller_filter_datePager extends tx_ptlist_filter {

    /**
     *
     */
	protected $value = array();

	/**
	 * MVC init method
	 *
	 * Checks if the column collection contains exactly one column as this filter
	 * can be used only with one column at the same time.
	 *
	 * @param   void
	 * @return  void
	 * @throws  tx_pttools_exceptionAssertion  if more than one column is attached to the filters columnCollection
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-09-08
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
	 * @param   void
	 * @return  string HTML output
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-09-08
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
	 * @param   void
	 * @return  string	HTML output for Smarty template
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-09-08
	 */
	public function isNotActiveAction() {

		// Get View
		$view = $this->getView('filter_datePager_userInterface');

        // Get Typoscript configuration values
        $span['range'] = $this->conf['range'] == '' ? 'day' : $this->conf['range'];
        $span['nextValue'] = isset($this->value['nextValue']) ? intval($this->value['nextValue']) : 1;
        $span['prevValue'] = isset($this->value['prevValue']) ? intval($this->value['prevValue']) : -1;
        $span['labelPrevious'] = $this->conf['labelPrevious'] == '' ? '<' : $GLOBALS['TSFE']->cObj->stdWrap($this->conf['labelPrevious'],  $this->conf['labelPrevious.']);
        $span['labelNext'] = $this->conf['labelNext'] == '' ? '>' : $GLOBALS['TSFE']->cObj->stdWrap($this->conf['labelNext'],  $this->conf['labelNext.']);

   		// Set View items for Smarty template
		$view->addItem($span, 'span');


		return $view->render();

    }


	/**
	 * Validate function
	 *
	 * This function is called by the parent::submitAction() method.
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

		// Save the incoming parameters to derived value property here.
        $this->value['mode'] = $this->params['mode'];

        // Increment or decrement pager values
        switch ($this->value['mode']) {
        case 'next':
            $this->value['value'] = $this->params['nextValue'];
            $this->value['nextValue'] = intval($this->params['nextValue']) + 1;
            $this->value['prevValue'] = $this->value['nextValue'] - 2;
            break;
        case 'prev':
            $this->value['value'] = $this->params['prevValue'];
            $this->value['prevValue'] = intval($this->params['prevValue']) - 1;
            $this->value['nextValue'] = $this->value['prevValue'] + 2;
            break;
        default:
			throw new tx_pttools_exceptionConfiguration("No valid 'mode' set in GET parameters.");
        }



		// Let the parent action do the submission.
		// It calls the validate() function.
		return parent::submitAction();
	}


	/**
	 * Get SQL where clause snippet
	 *
	 * This is an inherited abstract function from parent class tx_ptlist_filter.
	 * Thus it has to be implemented in this class.
	 *
	 * @param   void
	 * @return  string	SQL where clause snippet
	 * @author  Joachim Mathes <mathes@punkt.de>
	 * @since   2009-09-08
	 */
	public function getSqlWhereClauseSnippet() {

        $table = $this->dataDescriptions->getItemByIndex(0)->get_table();
		$column =  $table . '.' . $this->dataDescriptions->getItemByIndex(0)->get_field();

        $range = $this->conf['range'] == '' ? 'day' : $this->conf['range'];


		// Determine field type of date field (timestamp or date format; default: timestamp).
		// This information has to be given in the TypoScript config property 'dateFieldType'.
        $sqlFunction = $this->conf['dateFieldType'] == 'date' ? 'DATE_FORMAT' : 'FROM_UNIXTIME';

        // mktime parameters: hour, minute, second, month, day, year
		switch ($range) {
		case 'day':
			$sqlWhereClauseSnippet = $sqlFunction . "(" . $column . ", '%Y-%m-%d') = '" . date('Y-m-d', mktime(0, 0 ,0, date('m'), date('d') + intval($this->value['value']), date('Y'))) . "'";
			break;
		case 'week':
			$sqlWhereClauseSnippet = "WEEKOFYEAR(" . $sqlFunction . "(" . $column . ", '%Y-%m-%d')) = '" . date('W', mktime(0, 0 ,0, date('m'), date('d') + 7 * intval($this->value['value']), date('Y'))) . "' AND " . $sqlFunction . "(" . $column . ", '%Y') = '" . date('Y', mktime(0, 0 ,0, date('m'), date('d') + 7 * intval($this->value['value']), date('Y'))) . "'";
			break;
		case 'month':
            $sqlWhereClauseSnippet = $sqlFunction . "(" . $column . ", '%Y-%m') = '" . date('Y-m', mktime(0, 0, 0 , date('m') + intval($this->value['value']), date('d'), date('Y'))) . "'";
			break;
		case 'year':
			$sqlWhereClauseSnippet =  $sqlFunction . "(" . $column . ", '%Y') = '" . date('Y', mktime(0, 0, 0, date('m'), date('d'), date('Y') + intval($this->value['value']))) . "'";
			break;
		default:
			throw new tx_pttools_exceptionConfiguration("No valid 'range' set in Typoscript configuration.");
		}

		return $sqlWhereClauseSnippet;
    }


}