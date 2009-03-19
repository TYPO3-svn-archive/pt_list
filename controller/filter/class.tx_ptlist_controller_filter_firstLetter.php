<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2009 Fabrizio Branca (branca@punkt.de), Ursula Klingr (klinger@punkt.de)
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
require_once t3lib_extMgm::extPath('pt_list').'view/filter/firstLetter/class.tx_ptlist_view_filter_firstLetter_userInterface.php';

/**
 * First Letter filter class
 * 
 * @version 	$Id$
 * @author		Fabrizio Branca <branca@punkt.de>, Ursula Klinger <klinger@punkt.de>
 * @since		2009-01-23
 */
class tx_ptlist_controller_filter_firstLetter extends tx_ptlist_filter {

	/**
	 * MVC init method:
	 * Checks if the column collection contains exactly one column as this filter can be used only with one column at the same time
	 *
	 * @param 	void
	 * @return 	void
	 * @throws	tx_pttools_exceptionAssertion	if more than one column is attached to the filters columnCollection
	 * @author	Fabrizio Branca <branca@punkt.de>
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
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-01-19
	 */
	public function defaultAction() {
		
		$view = $this->getView('filter_firstLetter_userInterface');

		// retrieve list object from regitry
		$listObject = tx_pttools_registry::getInstance()->get($this->listIdentifier.'_listObject'); /* @var $listObject tx_ptlist_list */
		
		// prepare parameters for the "getGroupData" call
		$select = sprintf('UPPER(LEFT(%1$s,1)) as value, UPPER(LEFT(%1$s,1)) as label, count(*) as quantity', $this->dataDescriptions->getItemByIndex(0)->getSelectClause(false));
		$where  = '';  // sprintf('%s <> ""', $this->dataDescriptions->getItemByIndex(0)->get_identifier());
		$groupBy = 'value';
		$orderBy = 'value';
		$limit = '';
		$ignoredFiltersForWhereClause = $this->filterIdentifier; // ignores itself while retrieve where clause from (other) filters
		
		$possibleValues = $listObject->getGroupData($select, $where, $groupBy, $orderBy, $limit, $ignoredFiltersForWhereClause);
		
		if (!empty($this->value)) {
			// check if current value is under the possible values, if not reset the filter
			// this could be the case if the set of possible values changes because of restrictions from other filters
			$valueFound = false;
			foreach ($possibleValues as $possibleValue) {
				if ($possibleValue['value'] == $this->value) {
					$valueFound = true;
					break; 
				}
			}
			
			if (!$valueFound) {
				$this->reset();
			}
		}
		
		// prepend "all" value to reset the filter
		array_unshift(
			$possibleValues,
			array(
				'value' => 'reset',
				'label' => 'LLL:EXT:pt_list/locallang.xml:filter_group_all',
			)
		);
		
		// fill view 
		$view->addItem($possibleValues, 'possibleValues');
		$view->addItem($this->value, 'value');
		
		// render!
		return $view->render();
	}
	
	

	/**
	 * Submit action
	 * - calls 'default' action afterwards
	 * 
	 * @param 	void
	 * @return 	string	HTML output
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-01-23
	 */
	public function submitAction() {
		$this->isActive = true;
		$this->value = $this->params['value'];
		if ($this->value == 'reset') {
			return $this->doAction('reset');
		} else {
			return $this->doAction('default');
		}
	}
	
	
	
	
	/***************************************************************************
	 * Methods implementing abstract methods from "tx_ptlist_filter"
	 **************************************************************************/
	
	/**
	 * Get the sql where clause snippet for this filter
	 *
	 * @param 	void
	 * @return 	string 	where clause snippet
	 * @author	Fabrizio Branca <branca@punkt.de>, Ursula Klinger <klinger@punkt.de>
	 * @since	2009-01-23
	 */
	public function getSqlWhereClauseSnippet() {
        $sqlWhereClauseSnippet = sprintf('%s.%s LIKE "%s%%"', $this->dataDescriptions->getItemByIndex(0)->get_table(), $this->dataDescriptions->getItemByIndex(0)->get_field(), $this->value);
        return $sqlWhereClauseSnippet;
	}
	
}

?>