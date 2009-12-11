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



/**
 * Class definition file for group filter
 * 
 * @version     $Id$
 * @author     Fabrizio Branca <mail@fabrizio-branca.de>
 * @since       2009-01-23
 */



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_list').'controller/filter/options/class.tx_ptlist_controller_filter_options_base.php';



/**
 * Group filter class
 * 
 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
 * @since		2009-01-23
 * @package     TYPO3
 * @subpackage  pt_list\controller\filter
 */
class tx_ptlist_controller_filter_options_group extends tx_ptlist_controller_filter_options_base {


	/**
	 * Get options for this filter
	 *
	 * @param 	void
	 * @return 	array	array of array('item' => <value>, 'label' => <label>, 'quantity' => <quantity>)
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-21
	 */
	public function getOptions() {

		// retrieve list object from regitry
		$listObject = tx_pttools_registry::getInstance()->get($this->listIdentifier.'_listObject'); /* @var $listObject tx_ptlist_list */

		// prepare parameters for the "getGroupData" call
		$select = sprintf('%1$s as item, %1$s as label, count(*) as quantity', $this->dataDescriptions->getItemByIndex(0)->getSelectClause(false));
		$where = '';
		$groupBy = 'item';
		$orderBy = !empty($this->conf['orderBy']) ? $this->conf['orderBy'] : 'item ASC';
		$orderBy = $GLOBALS['TYPO3_DB']->quoteStr($this->conf['orderBy'], '');
		$limit = '';

		// ignore filters from configuration
		$ignoredFiltersForWhereClause = $this->conf['ignoreFilters'];

		// apend itself to the list
		$ignoredFiltersForWhereClause .= empty($ignoredFiltersForWhereClause) ? '' : ', ';
		$ignoredFiltersForWhereClause .= $this->filterIdentifier;

		return $listObject->getGroupData($select, $where, $groupBy, $orderBy, $limit, $ignoredFiltersForWhereClause);
	}

}

?>