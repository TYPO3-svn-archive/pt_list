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
 * Classes implementing this interface provide information about their internal column structure
 * 
 * All classes that implement this interface should implement the tx_pttools_iPageable interface too
 * 
 * tx_ptlist_iListable = information about columns (vertical)
 * tx_pttools_iPageable = information about rows (horizontal)
 * 
 * @author Fabrizio Branca <mail@fabrizio-branca.de>
 * @package     TYPO3
 * @subpackage  pt_list\model\interfaces
 */
interface tx_ptlist_iListable {
	
	/**
	 * Get a tx_ptlist_columnDescriptionCollection of all columns
	 * 
	 * @return  tx_ptlist_columnDescriptionCollection
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-14
	 */
	public function getAllColumnDescriptions();
	
	/**
	 * Get a tx_ptlist_columnDescriptionCollection of sortable columns (objects in this collection must be references to objects in the collection returned by getAllColumnDescriptions())
	 * 
	 * @return  tx_ptlist_columnDescriptionCollection
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
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