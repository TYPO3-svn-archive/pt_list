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
 * Class definition file for abstract filter value class
 * 
 * @version     $Id:$
 * @author      Michael Knoll
 * @since       2009-10-19
 */



/**
 * Class implementing an abstract filter value. 
 * 
 * @package     Typo3
 * @subpackage  pt_list
 * @author      Michael Knoll <knoll@punkt.de>
 * @since       2009-10-19
 */
abstract class tx_ptlist_filterValue {

	/**
	 * Holds actual filter value
	 *
	 * @var mixed
	 */
	protected $value;
	
	
	
	/**
	 * Sets filter value
	 *
	 * @param  mixed   $value      Value to be set as filter value
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-10-19
	 */
	public function setValue($value) {
		
		$this->value = $value;
		
	}

	
	
	/**
	 * Returns raw filter value without any transformation
	 *
	 * @return mixed   Raw filter value
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-10-19
	 */
	public function getRawValue() {
		
		return $this->value;
		
	}
	
	
	
	/**
	 * Returns URL encoded filter value.
	 * 
	 * @return mixed   URL encoded filter value
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-10-19
	 */
	abstract public function getUrlEncodedValue();
	
	
	
	/**
	 * Returns HTML escaped filter value.
	 * 
	 * @return mixed   HTML encoded filter value
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-10-19
	 */
	abstract public function getHtmlEncodedValue();
	
	
	
	/**
	 * Returns SQL escaped filter value.
	 * 
	 * @return mixed   SQL escaped filter value
     * @author         Michael Knoll <knoll@punkt.de>
     * @since          2009-10-19
	 */
	abstract public function getSqlEncoded();
	
	
}

?>