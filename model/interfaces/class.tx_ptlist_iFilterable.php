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
 * Defines the methods to use for a filter to interact with e.g. the list
 *
 * @package     TYPO3
 * @subpackage  pt_list\model\interfaces
 */
interface tx_ptlist_iFilterable {
	
	/**
	 * Returns a tx_ptlist_filterCollection that holds all filter configured for the list object that implements this interface
	 * 
	 * @return	tx_ptlist_filterCollection
	 */
	public function getAllFilters();
	
	/**
	 * Get group data.
	 * This method allows filters to get some information about the current state of the list object
	 *
	 * @param 	string	select clause
	 * @param 	string	(optional) where clause
	 * @param 	string	(optional) group by clause
	 * @param 	string	(optional) order by clause
	 * @param 	string	(optional) limit clause
	 * @param 	string	(optional) csl of filter identifiers taht should be ignored when retrivieving the (other) filter's where clauses
	 * @return 	array 	array of records
	 */
	public function getGroupData($select, $where='', $groupBy='', $orderBy='', $limit='', $ignoredFiltersForWhereClause='');
	
}

?>