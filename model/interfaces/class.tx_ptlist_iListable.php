<?php

/**
 * Classes implementing this interface provide information about their internal column structure
 * 
 * All classes that implement this interface should implement the tx_pttools_iPageable interface too
 * 
 * tx_ptlist_iListable = information about columns (vertical)
 * tx_pttools_iPageable = information about rows (horizontal)
 */
interface tx_ptlist_iListable {
	
	/**
	 * Get a tx_ptlist_columnDescriptionCollection of all columns
	 * 
	 * @return  tx_ptlist_columnDescriptionCollection
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-01-14
	 */
	public function getAllColumnDescriptions();
	
	/**
	 * Get a tx_ptlist_columnDescriptionCollection of sortable columns (objects in this collection must be references to objects in the collection returned by getAllColumnDescriptions())
	 * 
	 * @return  tx_ptlist_columnDescriptionCollection
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-01-14
	 */
	public function getSortableColumns();
	
	/**
	 * Get a list identifier
	 * 
	 * @return 	string
	 */
	public function getListId();
	
}
?>