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

require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_objectCollection.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iTemplateable.php';

require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_bookmarkAccessor.php';
require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_bookmarkCollection.php';

class tx_ptlist_bookmarkCollection extends tx_pttools_objectCollection implements tx_pttools_iTemplateable {
    
	protected $restrictedClassName = 'tx_ptlist_bookmark';
	
	public function loadBookmarksForFeuser($feuser_uid, $list) {
		$rows = tx_ptlist_bookmarkAccessor::getInstance()->selectBookmarksForFeUser($feuser_uid, $list);
		foreach ($rows as $row) {
			$this->addItem(new tx_ptlist_bookmark(NULL, $row));
		}
	}
    
    public function getMarkerArray() {
    	$markerArray = array();
    	foreach ($this as $key => $bookmark) { /* @var $bookmark tx_ptlist_bookmark */
    		$markerArray[$key] = $bookmark->getMarkerArray();
    	}
    	return $markerArray;
    }
	
}

?>