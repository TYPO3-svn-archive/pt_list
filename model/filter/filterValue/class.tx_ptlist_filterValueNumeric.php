<?php
/***************************************************************
 *  Copyright notice
 *  
 *  (c) 2009 Michael Knoll (knoll@punkt.de)
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
 * Class definition file for filter value class for scalar values
 * 
 * @version     $Id:$
 * @author      Michael Knoll
 * @since       2009-10-19
 */



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_list') . 'model/filter/filterValue/class.tx_ptlist_filterValue.php';



/**
 * Class implementing a numeric filter value. 
 * 
 * @package     Typo3
 * @subpackage  pt_list
 * @author      Michael Knoll <knoll@punkt.de>
 * @since       2009-11-12
 */
class tx_ptlist_filterValueNumeric extends tx_ptlist_filterValue {
	
	/**
	 * Sets value of this filter
	 *
	 * @param      float   $value  Value of filter
     * @author     Michael Knoll <knoll@punkt.de>
     * @since      2009-11-12
	 */
	public function setValue($value) {
	    tx_pttools_assert::isNumeric($value);
	    parent::setValue($value);
	}
	
	
	
	/**
     * Returns URL encoded filter value.
     * 
     * @return mixed   URL encoded filter value
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-11-12
     */
	public function getUrlEncodedValue() {
		if ($this->value != '') {
		  return floatval($this->value);
		} else {
			return NULL;
		}
	}
	
	
	
	/**
     * Returns HTML escaped filter value.
     * 
     * @return mixed   HTML encoded filter value
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-11-12
     */
	public function getHtmlEncodedValue() {
		return $this->getUrlEncodedValue();
	}
	
	
	
	/**
     * Returns SQL escaped filter value.
     * 
     * @return mixed   SQL escaped filter value
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-11-12
     */
	public function getSqlEncodedValue() {
		return $this->getUrlEncodedValue();
	}
	
}

?>