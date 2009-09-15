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
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_div.php';

/**
 * Class implementing a Date Pager filter
 *
 * @version  $Id$
 * @author   Joachim Mathes
 * @since    2009-09-08
 */
class tx_ptlist_controller_filter_datePager extends tx_ptlist_filter {

	protected $value = array();
    protected $firstDayOfWeek = 1; // set index to monday which is the first day of the week according to DIN 1355 

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
        $span['entity'] = $this->conf['entity'] == '' ? 'day' : $this->conf['entity'];
        $span['nextValue'] = isset($this->value['nextValue']) ? intval($this->value['nextValue']) : 1;
        $span['prevValue'] = isset($this->value['prevValue']) ? intval($this->value['prevValue']) : -1;
        $span['labelPrevious'] = $this->conf['labelPrevious'] == '' ? '<' : $GLOBALS['TSFE']->cObj->stdWrap($this->conf['labelPrevious'],  $this->conf['labelPrevious.']);
        $span['labelNext'] = $this->conf['labelNext'] == '' ? '>' : $GLOBALS['TSFE']->cObj->stdWrap($this->conf['labelNext'],  $this->conf['labelNext.']);
        $span['header'] = $this->renderHeader();

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
        $dateFieldType = $this->conf['dateFieldType'];
        $entity = $this->conf['entity'] == '' ? 'day' : $this->conf['entity'];

		// Determine field type of date field (timestamp or date format; default: timestamp).
		// This information has to be given in the TypoScript config property 'dateFieldType'.
        switch ($dateFieldType) {
        case 'date':
            $sqlFunction = 'DATE_FORMAT';
            break;
        case 'timestamp':
            $sqlFunction = 'FROM_UNIXTIME';
            break;
		default:
			throw new tx_pttools_exceptionConfiguration("No valid 'dateFieldType' set in Typoscript configuration.");            
        }

        // mktime parameters: hour, minute, second, month, day, year
		switch ($entity) {
		case 'day':
			$sqlWhereClauseSnippet = $sqlFunction . "(" . $column . ", '%Y-%m-%d') = '" . date('Y-m-d', mktime(0, 0 ,0, date('n'), date('j') + intval($this->value['value']), date('Y'))) . "'";
			break;
		case 'week':
			$sqlWhereClauseSnippet = "WEEKOFYEAR(" . $sqlFunction . "(" . $column . ", '%Y-%m-%d')) = '" . date('W', mktime(0, 0 ,0, date('n'), date('j') + 7 * intval($this->value['value']), date('Y'))) . "' AND " . $sqlFunction . "(" . $column . ", '%Y') = '" . date('Y', mktime(0, 0 ,0, date('n'), date('j') + 7 * intval($this->value['value']), date('Y'))) . "'";
			break;
		case 'month':
            $sqlWhereClauseSnippet = $sqlFunction . "(" . $column . ", '%Y-%m') = '" . date('Y-m', mktime(0, 0, 0 , date('n') + intval($this->value['value']), date('j'), date('Y'))) . "'";
			break;
		case 'year':
			$sqlWhereClauseSnippet =  $sqlFunction . "(" . $column . ", '%Y') = '" . date('Y', mktime(0, 0, 0, date('n'), date('j'), date('Y') + intval($this->value['value']))) . "'";
			break;
		default:
			throw new tx_pttools_exceptionConfiguration("No valid 'entity' set in Typoscript configuration.");
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

        // Get configuration from TypoScript
        $entity = $this->conf['entity'] == '' ? 'day' : $this->conf['entity'];
        $beginFormat = $this->conf['beginFormat'] == '' ? 'Y-m-d' : $this->conf['beginFormat'];
        $endFormat = $this->conf['endFormat'] == '' ? 'Y-m-d' : $this->conf['endFormat'];

        $firstDayOfWeek = $this->firstDayOfWeek;

        // Evaluate day, week or month number depending on 'entity'
		switch ($entity) {
		case 'day':
            $dateEntityBegin =  date('d',
                                     mktime(0, 0, 0,
                                            date('n'),
                                            date('j') + intval($this->value['value']),
                                            date('Y')));
            $dateEntityEnd =  date('d',
                                   mktime(0, 0, 0,
                                          date('n'),
                                          date('j') + intval($this->value['value']),
                                          date('Y')));
			break;
		case 'week':
			$value = date('W', mktime(0, 0, 0, date('n'), date('j') + 7 * intval($this->value['value']), date('Y')));
            $dateEntityBegin = date($beginFormat,
                                    mktime(0, 0, 0,
                                           date('n'),
                                           date('j') + 7 * intval($this->value['value']) - date('w') + $firstDayOfWeek,
                                           date('Y')));
            $dateEntityEnd = date($endFormat,
                                  mktime(0, 0, 0,
                                         date('n'),
                                         date('j') + 7 * intval($this->value['value']) - date('w') + $firstDayOfWeek  + 6,
                                         date('Y')));
			break;
		case 'month':
            $dateEntityBegin = date($beginFormat,
                                    mktime(0, 0, 0,
                                           date('n') + intval($this->value['value']),
                                           1,
                                           date('Y')));
            $dateEntityEnd = date($endFormat,
                                  mktime(0, 0, 0,
                                         date('n') + intval($this->value['value']),
                                         date('t'),
                                         date('Y')));
			break;
		case 'year':
            $dateEntityBegin = date($beginFormat,
                                    mktime(0, 0, 0,
                                           1, 1, date('Y')));
            $dateEntityEnd = date($endFormat,
                                  mktime(0, 0, 0,
                                         12, 31, date('Y')));
			break;
		default:
			throw new tx_pttools_exceptionConfiguration("No valid 'entity' set in Typoscript configuration.");
		}

        $renderValues = array('begin' => $dateEntityBegin,
                              'end' => $dateEntityEnd);

		// Rendering configuration
		// Fields will be rendered with the tx_ptlist_div::renderValues() method. Have a look at the comment there for details
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


}