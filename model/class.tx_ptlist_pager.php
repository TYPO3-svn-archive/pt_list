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
 * Class definition file for pt_list pager class
 * $Id$
 * 
 * @author      Fabrizio Branca <mail@fabrizio-branca.de>
 * @since       2009-01-27 
 */



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iTemplateable.php';



/**
 * Pager class for pt_list
 * 
 * @package     TYPO3
 * @subpackage  pt_list\model
 * @author      Fabrizio Branca <mail@fabrizio-branca.de>
 * @since       2009-01-27
 */
class tx_ptlist_pager implements tx_pttools_iTemplateable {
	
	
	
	/**
	 * @var int	current page number
	 */
	protected $currentPageNumber = 1;
	
	/**
	 * @var int	items per page
	 */
	protected $itemsPerPage = 0;

	/**
	 * @var int
	 */
	protected $maxRows;
	
	/**
	 * @var int	resulting amount of pages (for the given itemsPerPage and total count of items in the collection)
	 */
	protected $amountPages;
	
	/**
	 * @var int	total amount of items in the collection
	 */
	protected $totalItemCount;
	
	/**
	 * @var tx_pttools_iPageable
	 */
	protected $itemCollection;
	
	/**
	 * @var tx_ptlist_iPagerStrategy
	 */
	protected $pagerStrategy;
	
	
	
	/**
	 * Sets amount of items per page
	 * 
	 * @param  int     $itemsPerPage   Number of items to be displayed on page
	 * @return void
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since  2009-01-20
	 */
	public function set_itemsPerPage($itemsPerPage) {
		$this->itemsPerPage = $itemsPerPage;
		
		// (re)calculate amount of pages
		if (!empty($this->totalItemCount)) {
			$this->amountPages = $this->calculateAmountPages($this->totalItemCount, $this->itemsPerPage);
		}
	}

	
	
	/**
	 * Sets the maximum number of rows
	 * 
	 * @param  int     $maxRows    Maximum number of rows
	 * @return void
     * @author Fabrizio Branca <mail@fabrizio-branca.de>
     * @since  2009-01-27
	 */
	public function set_maxRows($maxRows) {
		$this->maxRows = $maxRows;
	}
	
	
	
	/**
	 * Sets the pager strategy used to generate links etc.
	 * 
	 * @param  tx_ptlist_iPagerStrategy $pagerStrategy  Pager strategy object
	 * @return void
     * @author Fabrizio Branca <mail@fabrizio-branca.de>
     * @since  2009-01-27
	 */
	public function set_pagerStrategy($pagerStrategy) {
		$this->pagerStrategy = $pagerStrategy;
	}

	
	
	/**
	 * Calculates the amount of pages by total amount of items and number of items per page
	 * 
	 * @param  int     $totalItemCount     Total number of items
	 * @param  int     $itemsPerPage       Number of items per page
	 * @return int                         Number of pages
     * @author      Fabrizio Branca <mail@fabrizio-branca.de>
     * @since       2009-01-27
	 */
	public function calculateAmountPages($totalItemCount, $itemsPerPage) {
		return empty($itemsPerPage) ? 1 : ceil($totalItemCount / $itemsPerPage);
	}
	
	
	
	/**
	 * Set object itemCollection
	 *
	 * @param 	tx_pttools_iPageable $objectitemCollection
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-20
	 */
	public function set_itemCollection(tx_pttools_iPageable $itemCollection) {
		$this->itemCollection = $itemCollection;
		$this->totalItemCount = $itemCollection->getTotalItemCount(); // "getTotalItemCount()" is defined by the tx_ptlist_iPageable" interface
		if (!empty($this->maxRows)) {
			$this->totalItemCount = min($this->maxRows, $this->totalItemCount);
		}
		$this->amountPages = $this->calculateAmountPages($this->totalItemCount, $this->itemsPerPage);
	}
	
	
	
	/**
	 * Set the current page number from outside (e.g. coming from a get parameter or from the session) 
	 *
	 * @param 	int	current page number
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-20
	 */
	public function set_currentPageNumber($currentPageNumber) {
		tx_pttools_assert::isValidUid($currentPageNumber, false, array('message' => 'No valid page number')); // TODO: choose better assertion
		$this->currentPageNumber = (int)$currentPageNumber;
	}
	
	
	
	/**
	 * Returns the total amount of items 
	 * 
	 * @param  void
	 * @return int     Total amount of items
     * @author Fabrizio Branca <mail@fabrizio-branca.de>
     * @since  2009-01-27
	 */
	public function get_totalItemCount() {
		return $this->totalItemCount;
	}
	
	
	
	/**
	 * Returns the item collection for the current page
	 * 
	 * @param 	void
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-20
	 */
	public function getItemCollectionForPage() {
		$offSetandRowCount = $this->getCurrentOffSetAndRowcount();
    	return $this->getItemCollection($offSetandRowCount['rowcount'], $offSetandRowCount['offset']);
	}

	
	
	/**
	 * Returns current offset and rowcount as an array ($result['offset'] and $result['rowcount'])
	 * 
	 * @param  void
	 * @return array   Current offset and rowcount
     * @author Fabrizio Branca <mail@fabrizio-branca.de>
     * @since  2009-01-27
	 */
	public function getCurrentOffSetAndRowcount() {
		$offSetandRowCount = array();
		if (!empty($this->itemsPerPage)) {
			$offSetandRowCount['offset'] = (($this->itemsPerPage) * ($this->currentPageNumber - 1));
			$offSetandRowCount['rowcount'] = $this->itemsPerPage;
		} else {
			$offSetandRowCount['offset'] = 0;
			$offSetandRowCount['rowcount'] = $this->totalItemCount;
		}
		return $offSetandRowCount;
	}



	/**
	 * Returns the itemCollection for a given offset and rowcount
	 *
	 * @param	int	offset
	 * @param	int	rowcount
	 * @return	Traversable
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-01-27
	 */
	public function getItemCollection($rowcount, $offset=NULL) {
		tx_pttools_assert::isInstanceOf($this->itemCollection, 'tx_pttools_iPageable', array('message' => 'No valid "itemCollection" found!'));
		return $this->itemCollection->getItems(self::getLimitFromRowcountAndOffset($rowcount, $offset));
	}

	

	/**
	 * Returns a LIMIT string for SQL clauses
	 * 
	 * @param  int     $rowcount   Number of records to be displayed
	 * @param  int     $offset     Offset to display records from
	 * @return string              SQL LIMIT string
     * @author Fabrizio Branca <mail@fabrizio-branca.de>
     * @since  2009-01-27
	 */
	public static function getLimitFromRowcountAndOffset($rowcount, $offset) {
		$limit = '';
		if (!empty($offset)) {
			$limit .= intval($offset).',';
			tx_pttools_assert::isValidUid($rowcount, false, array('message' => 'If an offset is set the rowcount cannot be empty!'));
		}
		if (!empty($rowcount)) {
			$limit .= intval($rowcount);
		}
		return $limit;
	}


	
	/**
	 * Get aggregated data
	 *
	 * @param	void
	 * @return	array	array(<aggregateDataDescriptionIdentifier> => <aggregateValue>)
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-20 
	 */
	public function getAggregateDataForPage() {
		tx_pttools_assert::isInstanceOf($this->itemCollection, 'tx_pttools_iPageable', array('message' => 'No valid "itemCollection" found!'));
		$offSetandRowCount = $this->getCurrentOffSetAndRowcount();
		return $this->itemCollection->getAllAggregates(self::getLimitFromRowcountAndOffset($offSetandRowCount['rowcount'], $offSetandRowCount['offset']));
	}
	
	
	
	/***************************************************************************
	 * Methods implementing the "tx_pttools_iTemplateable" interface
	 **************************************************************************/
	
	/**
	 * Get marker array
	 *
	 * @param 	void
	 * @return 	array	marker array
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-20
	 */
	public function getMarkerArray() {
	
		$markerArray = array(
			'currentPageNumber' => $this->currentPageNumber,
			'amountPages' => $this->amountPages,
			'totalItemCount' => $this->totalItemCount,
			'itemsPerPage' => $this->itemsPerPage,
		);
		$markerArray['offSetStart'] = ($this->totalItemCount == 0) ? 0 : (($this->itemsPerPage) * ($this->currentPageNumber - 1) +1);
		$markerArray['offSetEnd'] = min($markerArray['totalItemCount'], $markerArray['offSetStart'] + $markerArray['itemsPerPage'] -1);
		
		// process the links with the pager strategy 
		tx_pttools_assert::isInstanceOf($this->pagerStrategy, 'tx_ptlist_iPagerStrategy', array('message' => 'No pager strategy found!'));
		
		$this->pagerStrategy->setAmountPages($this->amountPages);
		$this->pagerStrategy->setCurrentPageNumber($this->currentPageNumber);
		
		$markerArray['pages'] = $this->pagerStrategy->getLinks();
		
		return $markerArray;
	}
		
}

?>