<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Fabrizio Branca (branca@punkt.de)
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


require_once t3lib_extMgm::extPath('pt_tools') . 'res/abstract/class.tx_pttools_iSettableByArray.php';

/**
 * Very simple and generic class, whose property can be filled by passing an array (with property names as array keys and property values as array values) to the constructor
 *
 * @version 	$Id: class.tx_ptlist_typo3Tables_dataObject.php,v 1.4 2009/02/15 16:14:56 ry44 Exp $
 * @author		Fabrizio Branca <branca@punkt.de>
 * @since		2009-01-20
 */
class tx_ptlist_typo3Tables_dataObject implements ArrayAccess, tx_pttools_iSettableByArray {

	protected $_data = array();

	/**
	 * Constructor
	 * Pass an array("propertyName" => "propertyValue", ...) to set the objects properties
	 *
	 * @param 	array 	property data
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-01-20
	 */
	public function __construct(array $row = array()) {
		if (!empty($row)) {
			$this->setPropertiesFromArray($row);
		}
	}



    /***************************************************************************
     * Methods implementing the tx_pttools_iSettableByArray interface
     **************************************************************************/

	/**
	 * Set properties from array
	 *
	 * @param 	array 	dataArray
	 * @return 	void
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-01-21
	 */
	public function setPropertiesFromArray(array $dataArray) {
		foreach ($dataArray as $key => $value) {
			$this->_data[$key] = $value;
		}
	}



	/***************************************************************************
	 * Methods implementing the "ArrayAccess" interface
	 **************************************************************************/
	public function offsetExists($offset) {
	 	return array_key_exists($offset, $this->_data);
	}

	public function offsetGet($offset) {
		return $this->_data[$offset];
	}

	public function offsetSet($offset, $value) {
		return $this->_data[$offset] = $value;
	}

	public function offsetUnset($offset) {
		unset($this->_data[$offset]);
	}
}

?>