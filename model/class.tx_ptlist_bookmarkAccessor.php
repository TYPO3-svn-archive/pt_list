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

require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_registry.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_assert.php';

/**
 * Database accessor for the dataObject class
 * 
 * @version 	$Id$
 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
 * @since		2009-01-22
 */
class tx_ptlist_bookmarkAccessor implements tx_pttools_iSingleton { 
	
	/**
	 * @var tx_ptlist_bookmarkAccessor
	 */
	private static $uniqueInstance = NULL;
    
    
    /***************************************************************************
     *   CONSTRUCTOR & OBJECT HANDLING METHODS
     **************************************************************************/
    
    /**
     * Returns a unique instance (Singleton) of the object. Use this method instead of the private/protected class constructor.
     *
     * @param   void
     * @return  tx_ptlist_bookmarkAccessor      unique instance of the object (Singleton) 
     * @author	Fabrizio Branca <mail@fabrizio-branca.de>
     * @since	2009-01-22
     */
    public static function getInstance() {
        
        if (self::$uniqueInstance === NULL) {
            $className = __CLASS__;
            self::$uniqueInstance = new $className;
        }
        return self::$uniqueInstance;
        
    }
    
    /**
     * Final method to prevent object cloning (using 'clone'), in order to use only the unique instance of the Singleton object.
     * @param   void
     * @return  void
     * @author	Fabrizio Branca <mail@fabrizio-branca.de>
     * @since	2009-01-22
     */
    public final function __clone() {
        trigger_error('Clone is not allowed for '.get_class($this).' (Singleton)', E_USER_ERROR);
    }
    
    
    /***************************************************************************
     *   ACCESSOR METHODS
     **************************************************************************/
        
    
    public function selectBookmarksForFeUser($feuser_uid, $list) {
    	
    	// query preparation
        $select  = '*';
        $from    = 'tx_ptlist_bookmarks';
        // TODO: aufräumen!
        $where   = '(feuser = "' . intval($feuser_uid) . '" OR feuser = "0" OR 1)';
        $where	.= ' AND list = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($list, $from);
        $where	.= tx_pttools_div::enableFields($from);
        
        // exec query using TYPO3 DB API
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where);
        tx_pttools_assert::isMySQLRessource($res);
        
        $rows = array();
        while (($a_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) == true) {
        	$rows[] = $a_row;
        }
        
        $GLOBALS['TYPO3_DB']->sql_free_result($res);

        return $rows;
    }    
    
    public function selectBookmarkByUid($bookmark_uid) {
    	
    	// query preparation
        $select  = '*';
        $from    = 'tx_ptlist_bookmarks';
        // TODO: aufräumen!
        $where   = 'uid = ' . intval($bookmark_uid);
        $where	.= tx_pttools_div::enableFields($from);
        
        // exec query using TYPO3 DB API
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where);
        tx_pttools_assert::isMySQLRessource($res);
        
        $a_row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
        
        $GLOBALS['TYPO3_DB']->sql_free_result($res);

        return $a_row;
    }
    
    
    
    public function insertBookmark(array $dataArray) {
        
        $insertFieldsArr = array();
        
        // query preparation
        $table = 'tx_ptlist_bookmarks';
        $insertFieldsArr['pid']             = tx_pttools_div::getTS('plugin.tx_ptlist.bookmarkStoragePid');
        $insertFieldsArr['tstamp']          = time();
        $insertFieldsArr['crdate']          = time();
        $insertFieldsArr['name'] 			= $dataArray['name'];
        $insertFieldsArr['list'] 			= $dataArray['list'];
        $insertFieldsArr['filterstates'] 	= $dataArray['filterstates'];

        // exec query using TYPO3 DB API
        $res = $GLOBALS['TYPO3_DB']->exec_INSERTquery($table, $insertFieldsArr);
        tx_pttools_assert::isMySQLRessource($res);
        
        $lastInsertedId = $GLOBALS['TYPO3_DB']->sql_insert_id();
        
        return $lastInsertedId;
    }
    
    	
}

?>