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
 * Class definition file for a default pager strategy implementation
 * 
 * $Id$
 * 
 * @author Fabrizio Branca <mail@fabrizio-branca.de>
 * @since 2009-01-27
 */



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_list').'model/interfaces/class.tx_ptlist_iPagerStrategy.php';



/**
 * Class implementing a default pager strategy
 * 
 * @version		$Id$
 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
 * @since		2009-01-27
 * @package     TYPO3
 * @subpackage  pt_list\model\pagerStrategy
 */
class tx_ptlist_pagerStrategy_default implements tx_ptlist_iPagerStrategy {

	
	
	/**
	 * @var array  Configuration array for pager strategy
	 */
	protected $conf;
	
	/**
	 * @var int    Holds current page number
	 */
	protected $currentPageNumber;
	
	/**
	 * @var int    Holds amount of pages
	 */
	protected $amountPages;
	
	
	
	/***************************************************************************
	 * Methods implementing the "tx_ptlist_iPagerStrategy" interface
	 **************************************************************************/
	
	
	
	/**
	 * Sets configuration of pager strategy
	 * 
	 * @param  array   $conf       Configuration of pager strategy
	 * @return void 
	 * @author      Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since       2009-01-27
	 */
	public function setConfiguration(array $conf) {
		$this->conf = $conf;
		
		// set defaults if no values are configured
		$this->conf['delta'] = isset($this->conf['delta']) ? $this->conf['delta'] : 0;		
		$this->conf['elements'] = isset($this->conf['elements']) ? $this->conf['elements'] : 'first, prev, pages, next, last';

		tx_pttools_assert::isValidUid($this->conf['delta'], true, array('message' => 'No valid "delta"!'));
		
	}
	
	
	
	/**
	 * Sets the current page number
	 * 
	 * @param  int  $currentPageNumber  Current Page number
	 * @return void
     * @author      Fabrizio Branca <mail@fabrizio-branca.de>
     * @since       2009-01-27
	 */
	public function setCurrentPageNumber($currentPageNumber) {
		$this->currentPageNumber = $currentPageNumber;
	}
	
	
	
	/**
	 * Sets the total amount of pages
	 * 
	 * @param  int $amountPages    Amount of pages
	 * @return void
     * @author      Fabrizio Branca <mail@fabrizio-branca.de>
     * @since       2009-01-27
	 */
	public function setAmountPages($amountPages) {
		$this->amountPages = $amountPages;	
	}
	
	
	
	/**
	 * Returns an array of links for paging
	 * 
	 * @param  void
	 * @return void
     * @author      Fabrizio Branca <mail@fabrizio-branca.de>
     * @since       2009-01-27
	 */
	public function getLinks() {
		$links = array();

		foreach (t3lib_div::trimExplode(',', $this->conf['elements']) as $element) {
			
			switch ($element) {
				case 'pages': {		
					for ($i=1; $i<=$this->amountPages; $i++) {
						// render the link only if it is in the delta (or if delta is off), if it is the first or the last one
						if ($this->conf['delta'] == 0 || abs($i-$this->currentPageNumber) <= $this->conf['delta'] || ($i == 1) || ($i == $this->amountPages) ) {
							$links[] = array(
								'pageNumber' => $i,
								'label' => $i,
								'current' => ($i == $this->currentPageNumber),
								'type' => 'pageitem',
							);
						} else {
							// append a "fill" item if it does not already exist
							$lastLinkItem = end($links);				
							if ($lastLinkItem['type'] != 'fillitem') {
								$links[] = array(
				            		'label' => 'EXT:pt_list/locallang.xml:pager_fill',
									'type' => 'fillitem'
								);
							}
						}
					}
				} break;
				
				case 'prev': {
					$prevpage = max($this->currentPageNumber - 1, 1);
					$links[] =  array(
		            	'pageNumber' => $prevpage,
		            	'label' => 'EXT:pt_list/locallang.xml:pager_prev',
						'current' => ($prevpage == $this->currentPageNumber),
		            	'type' => 'prev',
		            );
				} break;
				
				case 'next': {
					$nextpage = min($this->currentPageNumber + 1, $this->amountPages);
            		$links[] = array(
	            		'pageNumber' => $nextpage,
	            		'label' => 'EXT:pt_list/locallang.xml:pager_next',
						'current' => ($nextpage == $this->currentPageNumber),
	            		'type' => 'next',
	            	);
				} break; 
				
				case 'first': {
					$firstpage = 1;
			        $links[] = array(
		            	'pageNumber' => $firstpage,
		            	'label' => 'EXT:pt_list/locallang.xml:pager_first',
						'current' => ($firstpage == $this->currentPageNumber),
		            	'type' => 'first',
		            );
				} break;
				
				case 'last': {
            		$lastpage = $this->amountPages;
			        $links[] = array(
		            	'pageNumber' => $lastpage,
		            	'label' => 'EXT:pt_list/locallang.xml:pager_last',
						'current' => ($lastpage == $this->currentPageNumber),
		            	'type' => 'last',
		            );
					
				} break;

				case 'offsetinfo': {
					// nothing to be done here, except for appending a page element with the "offsetinfo" type
					// offset info data is available in the view under the keys offSetStart, offSetEnd, totalItemCount
					// this page element acts only as a placeholder...
			        $links[] = array(
		            	'type' => 'offsetinfo',
		            );

				} break;
				
				default: {
					throw new tx_pttools_exception('Invalid element!');
				}
			}
			
		}
		
		return $links;
	}
	
}

?>