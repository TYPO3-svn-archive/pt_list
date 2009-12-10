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

require_once t3lib_extMgm::extPath('pt_list').'model/typo3Tables/class.tx_ptlist_typo3Tables_dataObject.php';
require_once t3lib_extMgm::extPath('pt_list').'model/typo3Tables/class.tx_ptlist_typo3Tables_dataObjectAccessor.php';

require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_objectCollection.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iTemplateable.php';

/**
 * Data object collection
 *
 * @version 	$Id$
 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
 * @since		2009-01-22
 */
class tx_ptlist_typo3Tables_dataObjectCollection extends tx_pttools_objectCollection implements tx_pttools_iTemplateable {

    protected $restrictedClassName = 'tx_ptlist_typo3Tables_dataObject';

    /**
     * @var string	list identifier
     */
    protected $listId;


    /**
     * Class constructor
     *
     * @param 	string	list identifier
     * @author	Fabrizio Branca <mail@fabrizio-branca.de>
     * @since	2009-01-22
     */
    public function __construct($listId) {
    	$this->listId = $listId;
    }



    /**
     * Load items
     *
     * @param unknown_type $fields
     * @param unknown_type $where
     * @param unknown_type $orderBy
     * @param unknown_type $limit
     * @return 	void
     * @author	Fabrizio Branca <mail@fabrizio-branca.de>
     * @since	2009-01-22
     */
    public function loadItems($fields, $where = '', $orderBy = '', $limit = '', $groupBy = '') {
    	$rows = tx_ptlist_typo3Tables_dataObjectAccessor::getInstanceById($this->listId)->selectRows($this->listId, $fields, $where, $orderBy, $limit, $groupBy);

    	foreach ($rows as $row) {
    		$tmpDataObject = new tx_ptlist_typo3Tables_dataObject();
    		tx_pttools_assert::isInstanceOf($tmpDataObject, 'tx_pttools_iSettableByArray');
    		$tmpDataObject->setPropertiesFromArray($row);
    		$this->addItem($tmpDataObject);
    	}
    }

    /**
     * Get Marker array.
     *
     * @param 	void
     * @return 	arra	array of marker arrays of all elements in this collection
     * @author	Fabrizio Branca <mail@fabrizio-branca.de>
     * @since	2009-01-22
     */
    public function getMarkerArray() {
    	$items = array();
    	foreach ($this as $element) {
    		if ($element instanceof tx_pttools_iTemplateable) {
    			$items[] = $element->getMarkerArray();
    		} elseif ($element instanceof ArrayAccess) {
    			$items[] = $element;
    		} else {
    			throw new tx_pttools_exception('Element has to implement either the "tx_pttools_iTemplateable" or the "ArrayAccess" interface');
    		}
    	}
    	return $items;
    }

}

?>