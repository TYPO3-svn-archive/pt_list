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
 * Class implementing a scalar filter value. 
 * 
 * @package     Typo3
 * @subpackage  pt_list
 * @author      Michael Knoll <knoll@punkt.de>
 * @since       2009-10-19
 */
class tx_ptlist_filterValueString extends tx_ptlist_filterValue {

	
	public function setValue($value) {
	    tx_pttools_assert::isString($value);
	    parent::setValue($value);
	}
	
	
	
	/**
     * Returns URL encoded filter value.
     * 
     * @return mixed   URL encoded filter value
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-10-19
     */
	public function getUrlEncodedValue() {
		return urlencode($this->value);
	}
	
	
	
	/**
     * Returns HTML escaped filter value.
     * 
     * @return mixed   HTML encoded filter value
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-10-19
     */
	public function getHtmlEncodedValue() {
		return htmlentities($this->value);
	}
	
	
	
	/**
     * Returns SQL escaped filter value.
     * 
     * @return mixed   SQL escaped filter value
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-10-19
     */
	public function getSqlEncodedValue() {
		// TODO tablename is not required in current T3 implementation (2009-10-20) but could be reuquired in future implementations
		return self::sqlEncodeString($this->value,'');
	}
	
	
	
	/**
	 * Returns an array of SQL escaped filter values
	 * that have been splitted by $splitSymbol
	 *
	 * @param      string  $splitSymbol
	 * @return     void
     * @author     Michael Knoll <knoll@punkt.de>
     * @since      2009-11-12
	 */
	public function getSplittedByValueSqlEncoded($splitSymbol = ',') {
		$splittedEncodedArray = array();
	    $nonSplittedEncodedArray = split($splitSymbol,$this->value);
	    foreach($nonSplittedEncodedArray as $nonEncodedValue) {
	    	$splittedEncodedArray[] = self::sqlEncodeString($nonEncodedValue); 
	    }
	    return $splittedEncodedArray;
	}
	
	
	
	/***********************************************************************
	 * Helper methods
	 ***********************************************************************/
	
	/**
	 * Helper method for SQL encoding strings
	 *
	 * @param  string  $stringToBeEncoded  String that should be encoded
	 * @return string                      SQL Encoded string
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-12
	 */
	public static function sqlEncodeString($stringToBeEncoded) {
		return $GLOBALS['TYPO3_DB']->fullQuoteStr($stringToBeEncoded,'');
	}
	
}

?>