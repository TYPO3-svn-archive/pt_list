<?php
/**
 * Defines the methods to use for a filter to interact with e.g. the list
 *
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