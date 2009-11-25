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
 * Class definition file for filter value class for date values
 * 
 * Class only implements some formats, feel free to any required format!
 * 
 * @version     $Id:$
 * @author      Michael Knoll
 * @since       2009-11-18
 */



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_list') . 'model/filter/filterValue/class.tx_ptlist_filterValue.php';



/**
 * Class implementing a date filter value. 
 * 
 * TODO use constants for date formats!
 * 
 * @package     Typo3
 * @subpackage  pt_list
 * @author      Michael Knoll <knoll@punkt.de>
 * @since       2009-11-18
 */
class tx_ptlist_filterValueDate extends tx_ptlist_filterValue {
	
	
	/**
	 * Definition of constants
	 * @see http://de3.php.net/manual/en/function.date.php
	 */
	const DD_DOT_MM_DOT_YYYY_INPUT_FORMAT     = "d.m.Y";
    const DD_DOT_MM_DOT_YY_INPUT_FORMAT       = "d.m.y";
    const YYYY_DASH_MM_DASH_DD_INPUT_FORMAT   = "Y-m-d";
    const YY_DASH_MM_DASH_DD_INPUT_FORMAT     = "y-m-d";
    
    const DD_DOT_MM_DOT_YYYY_OUTPUT_FORMAT    = "d.m.Y";
    const DD_DOT_MM_DOT_YY_OUTPUT_FORMAT      = "d.m.y";
    const YYYY_DASH_MM_DASH_DD_OUTPUT_FORMAT   = "Y-m-d";
    const YY_DASH_MM_DASH_DD_OUTPUT_FORMAT     = "y-m-d";
	

	/**
	 * Holds raw value without converting to timestamp etc.
	 * 
	 * @var string
	 */
	protected $rawValue;
	
	
	
	/**
	 * Holds format of input date
	 *
	 * @var string
	 */
	protected $inputFormat;
	
	
	
	/**
	 * Holds a reference to a date object for converted date
	 *
	 * @var unknown_type
	 */
	protected $dateObj;
	
	
	
	/**
	 * Holds an array with parts of date
	 * 
	 * array('d' => $day_part,
	 *       'm' => $month_part,
	 *       'y' => $year_part)
	 *
	 * @var array
	 */
	protected $datePartArray;
	
	
	/***************************************************************
	 * Factory methods
	 ***************************************************************/
	
    /**
     * Returns a date value object for a given date value and a given format
     *
     * @param   string      $dateValue      Value to be converted as object
     * @param   string      $format         Format of input format
     * @return  tx_ptlist_filterValueDate   Date value object for given parameters
     */	
	public static function getInstanceByDateAndFormat($dateValue, $format) {
		$dateValueObject = new tx_ptlist_filterValueDate();
		$dateValueObject->setValueWithFormat($dateValue, $format);
		return $dateValueObject;
	}
	
	
	
	/**
	 * Sets a date value with default format 'd.m.Y'
	 *
	 * @param mixed $value Value to be set as date
	 * @return void
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-11-18
	 */
	public function setValue($value) {
	    tx_pttools_assert::isNotEmptyString($value);
	    $this->setValueWithFormat($value, self::DD_DOT_MM_DOT_YYYY_INPUT_FORMAT);
	}
	
	
	
	/**
	 * Sets a date value with a given format. 
	 * Default format is "d.m.Y"
	 *
	 * @param      string      $value      Date value to be set
	 * @param      string      $format     Date format to be set
	 * @return     void
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-11-18
	 */
	public function setValueWithFormat($value, $format) {
		tx_pttools_assert::isNotEmptyString($value);
        $this->inputFormat = $format;
		$this->rawValue = $value;
		$this->createDateObj();
	}
	
	
	
	/**
     * Returns URL encoded filter value.
     * 
     * @param  string  $format      Set format for desired output format (use constants of filter value class for format strings!)
     * @return mixed   URL encoded filter value
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-11-18
     */
	public function getUrlEncodedValue($format = '') {
		if ($format == '') $format = self::DD_DOT_MM_DOT_YYYY_OUTPUT_FORMAT;
        $dateString = $this->getDateStringFromDateObject($format);
        return urlencode($dateString);
	}
    
    
    
    /**
     * Returns raw filter value.
     * 
     * @return mixed   Raw filter value
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-11-18
     */
    public function getRawValue() {
        return $this->rawValue;
    }
	
	
	
	/**
     * Returns HTML escaped filter value.
     * 
     * @return string  HTML encoded filter value
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-11-18
     */
	public function getHtmlEncodedValue($format ='') {
		if ($format == '') $format = self::DD_DOT_MM_DOT_YYYY_OUTPUT_FORMAT;
		$dateString = $this->getDateStringFromDateObject($format);
		return htmlentities($dateString);
	}
	
	
	
	/**
     * Returns SQL escaped filter value.
     * 
     * @param  string  $format    Date format to be returned (@see http://de3.php.net/manual/en/function.date.php)
     * @return string  SQL escaped filter value
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-11-18
     */
	public function getSqlEncodedValue($format = '') {
		if ($format == '') $format = self::DD_DOT_MM_DOT_YYYY_OUTPUT_FORMAT;
		$dateString = $this->getDateStringFromDateObject($format);
		// TODO tablename is not required in current T3 implementation (2009-10-20) but could be reuquired in future implementations
		return $this->sqlEncodeString($dateString);
	}
	
	
	
	/**
	 * Returns date part array
	 * (use this for testing parser funcitonality!)
	 *
	 * @return array   Associative array with date parts
	 */
	public function getParsedValuesArray() {
		return $this->datePartArray;
	}
	
	
	
	/**
	 * Returns value from date by given format
	 * 
	 * @param  string  $format    Date format to be returned (@see http://de3.php.net/manual/en/function.date.php)
	 * @return string  Date value formatted by format
	 */
	public function getValueByFormat($format) {
		return date_format($this->dateObj, $format);
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
     * @since  2009-11-18
	 */
	public static function sqlEncodeString($stringToBeEncoded) {
		return $GLOBALS['TYPO3_DB']->fullQuoteStr($stringToBeEncoded,'');
	}
	
	
	
	/**
	 * Creates date object for existing date and format
	 * 
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-11-18
	 */
	protected function createDateObj() {
		/**
		 * This really sucks: PHP >= 5.3 has the following DateTime class:
		 * 
		 * $this->dateObj = DateTime::createFromFormat($this->rawValue, $this->inputFormat);
		 * 
		 * but this does not work in PHP < 5.3 so we have our own little conversion mechanism here
		 */
		$this->parseDate();
		$this->dateObj = date_create($this->createStringFromDateParts());
	}
	
	
	
	/**
	 * Parses a date into its parts (d, m, y)
	 * 
	 * TODO non-semantic parsing so far!
	 *
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-11-18
	 */
	protected function parseDate() {
	    
		switch ($this->inputFormat) {
            case self::DD_DOT_MM_DOT_YYYY_INPUT_FORMAT:    // matches '1.12.1989'
                $pattern = '/(\d{1,2})\.' .         // Day-part of date with 1 or 2 digits
                           '(\d{1,2})\.' .          // Month-part of date with 1 or 2 digits
                           '(\d{4})/';              // Year-part of date with 4 digits
                $matches = array();
                preg_match($pattern, $this->rawValue, $matches);
                tx_pttools_assert::isNotEmptyArray($matches);
                $this->datePartArray = array('d' => $matches[1], 'm' => $matches[2], 'Y' => $matches[3]);
            break;
                
            case self::DD_DOT_MM_DOT_YY_INPUT_FORMAT:    // matches '1.12.89'
                $pattern = '/(\d{1,2})\.' .         // Day-part of date with 1 or 2 digits
                           '(\d{1,2})\.' .          // Month-part of date with 1 or 2 digits
                           '(\d{2})/';              // Year-part of date with 2 digits
                $matches = array();
                preg_match($pattern, $this->rawValue, $matches);
                tx_pttools_assert::isNotEmptyArray($matches);
                $this->datePartArray = array('d' => $matches[1], 'm' => $matches[2], 'Y' => '20' . $matches[3]);
            break;
            
            case 'y-m-d':
                throw new tx_pttools_exceptionNotImplemented('Parser for y-m-d is not yet implemented!');
            break;
            
            case self::YYYY_DASH_MM_DASH_DD_INPUT_FORMAT:
                $pattern = '/(\d{4})\-' .         // Year-part of date with 4 digits
                           '(\d{1,2})\-' .        // Month-part of date with 1 or 2 digits
                           '(\d{1,2})/';          // Day-part of date with 1 or 2 digits
                $matches = array();
                preg_match($pattern, $this->rawValue, $matches);
                tx_pttools_assert::isNotEmptyArray($matches);
                $this->datePartArray = array('d' => $matches[3], 'm' => $matches[2], 'Y' => $matches[1]);
            break;
            
            case 'm/d/y':
                throw new tx_pttools_exceptionNotImplemented('Parser for m/d/y is not yet implemented!');
            break;
            
            case 'm/d/Y':
                throw new tx_pttools_exceptionNotImplemented('Parser for m/d/Y is not yet implemented!');
            break;
            
            default:
                throw new tx_pttools_exceptionInvalidArgument('Invalid date format!', 'Invalid date format: ' . $this->inputFormat);
            break;
        }
        
	}
	
	
	
	/**
	 * Creates an American date string from date parts
	 *
	 * @return string
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-11-18
	 */
	protected function createStringFromDateParts() {
		return $this->datePartArray['Y'] . '-' . $this->datePartArray['m'] . '-' . $this->datePartArray['d'] . ' 00:00:00'; 
	}
	
	
	
	/**
	 * Returns a string for date generated by current date object
	 *
	 * @param  string  $format     Desired format for date
	 * @return string              Formatted string for current date
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-11-18
	 */
	protected function getDateStringFromDateObject($format = 'd.m.Y') {
		return date_format($this->dateObj, $format);
	}
	
}

?>