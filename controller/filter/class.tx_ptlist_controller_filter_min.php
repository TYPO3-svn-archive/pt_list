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
require_once t3lib_extMgm::extPath('pt_list').'view/filter/min/class.tx_ptlist_view_filter_min_userInterface.php';

/**
 * Class implementing a "minimum" filter
 * 
 * @version  	$Id$
 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
 * @since		2009-01-26
 */
class tx_ptlist_controller_filter_min extends tx_ptlist_filter {
	
	
	
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
	
	
	
	/**
	 * Displays the user interface in active state
	 * - calls isNotActiveAction
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
	 * Display the interface
	 *
	 * @param 	void
	 * @return 	unknown
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	public function isNotActiveAction() {
		$view = $this->getView('filter_min_userInterface');
		$view->addItem($this->value, 'value');
		return $view->render();
	}
	
	
	
	/**
	 * Submit action
	 *
	 * @param 	void
	 * @return 	string	HTML output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	public function submitAction() {
		$this->isActive = true;
		$this->value = $this->params['value'];
		return $this->doAction('default');
	}

	
	
	/**
	 * Get sql where clause snippet
	 *
	 * @param 	void
	 * @return 	string 	sql where clause snippet
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	public function getSqlWhereClauseSnippet() {
		$sqlWhereClauseSnippet = sprintf('%s.%s >= %s', $this->dataDescriptions->getItemByIndex(0)->get_table(), $this->dataDescriptions->getItemByIndex(0)->get_field(), intval($this->value));
		return $sqlWhereClauseSnippet;
	}
	
}

?>