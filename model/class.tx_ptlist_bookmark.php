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



require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iSettableByArray.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iTemplateable.php';

class tx_ptlist_bookmark implements tx_pttools_iSettableByArray, tx_pttools_iTemplateable {
	
	protected $uid;
	protected $name;
	protected $list;
	protected $filterstates;
	
	/**
	 * TODO: offer a way to store bookmarks not only for single users or for all, but for fe_groups too
	 * 
	 * @var string	uid of the feuser this bookmark belongs to, 0=global bookmark
	 */
	protected $feuser = 0;
	
	public function __construct($uid=NULL, array $dataArray=array()) {
		
		if (!is_null($uid)) {
			// load from database
			$dataArray = tx_ptlist_bookmarkAccessor::getInstance()->selectBookmarkByUid($uid);
		} 
		
		if (!empty($dataArray)) {
			$this->setPropertiesFromArray($dataArray);
		}
	}
	
    public function setPropertiesFromArray(array $dataArray) {
    	$this->uid = $dataArray['uid'];
    	$this->name = $dataArray['name'];
    	$this->list = $dataArray['list'];
    	$this->filterstates = $dataArray['filterstates'];
    	$this->feuser = $dataArray['feuser'];
    }
    
    public function set_uid($uid) {
    	$this->uid = $uid;
    }
    
    public function set_name($name) {
    	$this->name = $name;
    }
    
    public function set_list($list) {
    	$this->list = $list;
    }
    
    public function set_filterstates($filterstates) {
    	$this->filterstates = $filterstates;
    }
    
    public function get_filterstate() {
    	return $this->filterstates;
    }
    
    public function set_feuser($feuser) {
    	$this->feuser = $feuser;
    }
    
    public function storeSelf() {
    	tx_ptlist_bookmarkAccessor::getInstance()->insertBookmark(get_object_vars($this));
    }
    
    public function getMarkerArray() {
    	return get_object_vars($this);
    }
    
    
	
}

?>