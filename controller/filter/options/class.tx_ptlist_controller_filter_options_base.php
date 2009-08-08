<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Fabrizio Branca (mail@fabrizio-branca.de)
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

require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_filter.php';
require_once t3lib_extMgm::extPath('pt_list').'view/filter/options/class.tx_ptlist_view_filter_options_userInterface_links.php';
require_once t3lib_extMgm::extPath('pt_list').'view/filter/options/class.tx_ptlist_view_filter_options_userInterface_select.php';
require_once t3lib_extMgm::extPath('pt_list').'view/filter/options/class.tx_ptlist_view_filter_options_userInterface_radio.php';
require_once t3lib_extMgm::extPath('pt_list').'view/filter/options/class.tx_ptlist_view_filter_options_userInterface_checkbox.php';
require_once t3lib_extMgm::extPath('pt_list').'view/filter/options/class.tx_ptlist_view_filter_options_userInterface_advmultiselect.php';


/**
 * Group filter class
 *
 * @version 	$Id$
 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
 * @since		2009-01-23
 */
abstract class tx_ptlist_controller_filter_options_base extends tx_ptlist_filter {

	protected $value = array();


	/**
	 * MVC init method:
	 * Checks if the column collection contains exactly one column as this filter can be used only with one column at the same time
	 *
	 * @param 	void
	 * @return 	void
	 * @throws	tx_pttools_exceptionAssertion	if more than one column is attached to the filters columnCollection
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-23
	 */
	public function init() {
		parent::init();
		tx_pttools_assert::isEqual(count($this->dataDescriptions), 1, array('message' => sprintf('This filter can only be used with 1 dataDescription (dataDescription found: "%s"', count($this->dataDescriptions))));
	}


	/***************************************************************************
	 * Action methods
	 **************************************************************************/

	/**
	 * Displays the user interface in active and inactive state.
	 * (Overwrite tx_ptlist_filter's defaultAction method, which wozuld call an isActiveAction or an isNotActiveAction)
	 *
	 * @param 	void
	 * @return 	string HTML output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	public function defaultAction() {

		// select view depeding on configured mode
		switch ($this->conf['mode']) {
			case '':
			case 'select': {
				$view = $this->getView('filter_options_userInterface_select');
			} break;

			case 'links': {
				$view = $this->getView('filter_options_userInterface_links');
			} break;

			case 'radio': {
				tx_pttools_assert::isTrue(empty($this->conf['multiple']), array('message' => 'Mode "radio" cannot be run in multiple mode!'));
				$view = $this->getView('filter_options_userInterface_radio');
			} break;

			case 'checkbox': {
				tx_pttools_assert::isFalse(empty($this->conf['multiple']), array('message' => 'Mode "checkbox" can only be run in multiple mode!'));
				$view = $this->getView('filter_options_userInterface_checkbox');
			} break;

			case 'advmultiselect': {
				tx_pttools_assert::isFalse(empty($this->conf['multiple']), array('message' => 'Mode "advmultiselect" can only be run in multiple mode!'));

				// include PEAR classes
				require_once 'HTML/QuickForm.php';
				require_once 'HTML/QuickForm/advmultiselect.php';

				$view = $this->getView('filter_options_userInterface_advmultiselect');
			} break;

			default: throw new tx_pttools_exception('"Mode" must be either "links", "radio", "checkbox", "advmultiselect" or "select" (default)!');
		}

		$possibleValues = $this->getOptions(); /* @var $possibleValues array of array('item' => <value>, 'label' => <label>, 'quantity' => <quantity>) */

		
		// render values
		
		if ((!empty($this->conf['renderObj']) && !empty($this->conf['renderObj.'])) ||  !empty($this->conf['renderUserFunctions.'])) {
			$renderConfig = array(
				'renderObj' => $this->conf['renderObj'],
				'renderObj.' => $this->conf['renderObj.'],
				'renderUserFunctions.' => $this->conf['renderUserFunctions.'],
			);
			foreach ($possibleValues as &$possibleValue) {
				$possibleValue['label'] = tx_ptlist_div::renderValues($possibleValue, $renderConfig);
			}
		}

		// mark active ones as active
		foreach ($possibleValues as &$possibleValue) {
			$possibleValue['active'] = is_array($this->value) && in_array($possibleValue['item'], $this->value);
		}

		/*
		if (!empty($this->value)) {
			// check if current value is under the possible values, if not reset the filter
			// this could be the case if the set of possible values changes because of restrictions from other filters
			$valueFound = false;
			foreach ($possibleValues as $possibleValue) {
				if ($possibleValue['item'] == $this->value) {
					$valueFound = true;
		-			break;
				}
			}

			if (!$valueFound) {
				$this->reset();
			}
		}
		*/

		// prepend "all" value to reset the filter
		// if (!empty($this->value)) {
		if ($this->conf['includeEmptyOption']) {
			if (!empty($this->conf['includeEmptyOption.']['label'])) {
				$label = $GLOBALS['TSFE']->sL($this->conf['includeEmptyOption.']['label']);
			} else {
				$label = $GLOBALS['TSFE']->sL('LLL:EXT:pt_list/locallang.xml:filter_group_all');
			}
			$class = !empty($this->conf['includeEmptyOption.']['class']) ? $this->conf['includeEmptyOption.']['class'] : 'empty-option';
			array_unshift(
				$possibleValues,
				array(
					'item' => 'reset',
					'label' => $label,
					'quantity' => '',
					'class' => $class,
				)
			);
		}
		//}

		// fill view
		$view->addItem($possibleValues, 'possibleValues');
		$view->addItem((array)$this->value, 'value');
		$view->addItem((bool)$this->isActive, 'filterActive');

		$view->addItem((bool)$this->conf['multiple'], 'multiple');
		$view->addItem((bool)$this->conf['submitOnChange'], 'submitOnChange');

		if (empty($this->conf['selectBoxSize'])) {
			$selectBoxSize = 1;
		} elseif (strtolower($this->conf['selectBoxSize']) == 'all') {
			$selectBoxSize = count($possibleValues);
		} else {
			tx_pttools_assert::isValidUid($this->conf['selectBoxSize'], false, array('message' => 'No valid selectBoxSize!'));
			$selectBoxSize = $this->conf['selectBoxSize'];
		}
		$view->addItem($selectBoxSize, 'selectBoxSize');

		// render!
		return $view->render();
	}



	/**
	 * Get options for this filter
	 *
	 * @param 	void
	 * @return 	array	array of array('item' => <value>, 'label' => <label>, 'quantity' => <quantity>)
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-21
	 */
	abstract public function getOptions();



	/**
	 * Submit action
	 * - calls 'default' action afterwards
	 *
	 * @param 	void
	 * @return 	string	HTML output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-23
	 */
	public function submitAction() {
		$this->isActive = true;
		if (is_array($this->params['value'])) {
		
			// multiple selection mode
			$this->value = array();
			foreach ($this->params['value'] as $key => $value) {
				$this->value[$key] = urldecode($value);
			}
			
		} elseif (isset($this->params['value'])) {
		
			// single selection mode	
			$this->params['value'] = urldecode($this->params['value']);

			if (!is_array($this->value)) $this->value = array();

			if (($this->conf['mode'] == 'links') && ($this->conf['toggleMode'] == true)) {
				if (($arrayKey = array_search($this->params['value'], $this->value)) !== false) {
					// already set => remove
					if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Value "%s" was already set in key "%s", removing it now.', $this->params['value'], $arrayKey), 'pt_list', 0, $this->value);
					unset($this->value[$arrayKey]);
				} else {
					// not set => add to array
					if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Value "%s" was not set, setting it now.', $this->params['value']), 'pt_list', 0, $this->value);
					$this->value[] = $this->params['value'];
				}
			} else {
				$this->value = array($this->params['value']);
			}
		} else {
			$this->value = array();
		}
		// TODO: don't use hardcoded value "reset" for resetting, but think of a better solution to this
		if (array_search('reset', $this->value) !==  false || empty($this->value)) {
			return $this->doAction('reset');
		} else {
			return $this->doAction('default');
		}
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
		$view = $this->getView('filter_breadcrumb');
		$view->addItem($this->label, 'label');
		
		$renderConfig = array(
			'renderObj' => $this->conf['renderObj'],
			'renderObj.' => $this->conf['renderObj.'],
			'renderUserFunctions.' => $this->conf['renderUserFunctions.'],
		);

		$values = array();
		foreach ((array)$this->value as $value) {
			$values[] = '"' . tx_ptlist_div::renderValues(array('item' => $value), $renderConfig) . '"';
		}

		$restOfValues = array_slice($values, 0, -2);
		$restOfValues[] = implode(' oder ', array_slice($values, -2));

		$view->addItem(implode(', ', $restOfValues), 'value');
		return $view->render();
	}



	/***************************************************************************
	 * Methods implementing abstract methods from "tx_ptlist_filter"
	 **************************************************************************/

	/**
	 * Get the sql where clause snippet for this filter
	 * Depending on $this->conf['multiple'] and $this->conf['findInSet']
	 *
	 * @param 	void
	 * @return 	string 	where clause snippet
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-23
	 */
	public function getSqlWhereClauseSnippet() {

		$field = $this->dataDescriptions->getItemByIndex(0)->getSelectClause(false);
		tx_pttools_assert::isNotEmptyString($field, array('message' => '"getSelectClause" returned invalid string!'));
		
		tx_pttools_assert::isNotEmptyArray($this->value, array('message' => 'Value array is empty!'));

		if ($this->conf['multiple'] == true) {
			if ($this->conf['findInSet'] == true) {
				$orSnippets = array();
				foreach ($this->value as $value) {
					$orSnippets[] = '('.sprintf('FIND_IN_SET("%2$s", %1$s)', $field, $value).')';
				}
				$sqlWhereClauseSnippet = '('.implode(' OR ', $orSnippets) .')';
			} else {
				$sqlWhereClauseSnippet = sprintf('%1$s IN ("%2$s")', $field, implode('", "', $this->value));
			}
		} else {
			if ($this->conf['findInSet'] == true) {
				$sqlWhereClauseSnippet = sprintf('FIND_IN_SET("%2$s", %1$s)', $field, $this->value[0]);
			} else {
				$sqlWhereClauseSnippet = sprintf('%1$s = "%2$s"', $field, $this->value[0]);
			}
		}

		return $sqlWhereClauseSnippet;
	}

}

?>