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
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_sessionStorageAdapter.php';
require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_externalDatabaseConnector.php';

/**
 * Generic database accessor
 *
 * @version 	$Id$
 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
 * @since		2009-01-22
 */
class tx_ptlist_genericDataAccessor {

	/**
	 * @var array	array of tx_ptlist_typo3Tables_dataObjectAccessor
	 */
	private static $uniqueInstances = array();

	/**
	 * @var t3lib_db
	 */
	protected $dbObj;

	/**
	 * @var bool	true if an external database is used
	 */
	protected $externalDatabase = false;
	
	protected $identifier;
	
	protected static $queryCache = array();


    /***************************************************************************
     *   CONSTRUCTOR & OBJECT HANDLING METHODS
     **************************************************************************/

    /**
     * Returns a unique instance (Singleton) of the object. Use this method instead of the private/protected class constructor.
	 * This implements a special type of singleton. Instead having exactly one access object, you will get a unique (butt different)
	 * accessor object per dsn.
     *
     * @param   string	identifier
     * @param 	string	(optional) dsn for external database
     * @return  tx_ptlist_genericDataAccessor      unique instance of the object (Singleton)
     * @author	Fabrizio Branca <mail@fabrizio-branca.de>
     * @since	2009-01-22
     */
    public static function getInstance($identifier, $dsn=NULL) {

		tx_pttools_assert::isNotEmptyString($identifier, array('message' => 'No "identifier" found!'));

		$className = __CLASS__;
        if (self::$uniqueInstances[$identifier] === NULL) {
			if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Creating new "%s" instance for identifier "%s"', $className, $identifier), 'pt_list');
            self::$uniqueInstances[$identifier] = new $className($dsn, $identifier);
        } else {
			if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Return existing "%s" instance for identifier "%s"', $className, $identifier), 'pt_list');
		}
        return self::$uniqueInstances[$identifier];
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
    
    public static function storeAllCachesToSession() {
    	foreach (self::$uniqueInstances as $accessor) { /* @var $accessor tx_ptlist_genericDataAccessor */
    		$accessor->storeCacheToSession();
    	}
    }

    
    
    /**
     * This class cannot be constructed manually. Use the getInstance() method!
     * 
     * @author	Fabrizio Branca <mail@fabrizio-branca.de>
     * @since	2009-02-24 
     */
	private function  __construct($dsn=NULL, $identifier=NULL) {
		
		$this->identifier = $identifier;
		
		if (is_null($dsn)) {
			// By default the TYPO3 database will be used. Use $this->setDatabase to switch to another one
			$this->dbObj = $GLOBALS['TYPO3_DB'];
		} else {
			$this->setDatabase($dsn);
		}
		
		// restore query cache from session
		self::$queryCache = tx_pttools_sessionStorageAdapter::getInstance()->read('querycache_genericDataAccessor_'.$this->identifier);
		if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Found query cache for "%s" in session with "%s" entries', $this->identifier, count(self::$queryCache)), 'pt_list', 1);
		
		
	}

	
	public function storeCacheToSession() {
		if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Storing query cache to session for accessor "%s" with "%s" entries', $this->identifier, count(self::$queryCache)), 'pt_list', 1);
		tx_pttools_sessionStorageAdapter::getInstance()->store('querycache_genericDataAccessor_'.$this->identifier, self::$queryCache);
	}
	



	/**
	 * Set database
	 *
	 * @param	string dsn, if equals to "<default>" the TYPO3 database will be used
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-24
	 */
    public function setDatabase($dsn) {
		$this->dbObj = new tx_ptlist_externalDatabaseConnector($dsn);
		$this->externalDatabase = true;
    }

    public function getRows($res, $freeResult = true) {
    	$rows = array();
    	while (($a_row = $this->dbObj->sql_fetch_assoc($res)) == true) {
        	$rows[] = $a_row;
        }
        if ($freeResult == true) {
        	$this->dbObj->sql_free_result($res);	
        }
        return $rows;
    }
    
    

    /***************************************************************************
     *   ACCESSOR METHODS
     **************************************************************************/
    
    /**
     * Checks the age of a cache entry.
     *
     * @param 	int		timestamp of the entry
     * @param 	int		(opional) maximum age in seconds, if 0 the entry is always valid
     * @return 	bool	true, if the entry of young enough, false otherwise
     */
    public function checkAge($tstamp, $maxAge=0) {
    	return ($maxAge > 0 && ($tstamp + $maxAge < time()));
    }
    
    
    public function select($select_fields, $from_table, $where_clause, $groupBy='', $orderBy='', $limit='', $forceCacheUpdate=false, $maxAge=0)	{
    	
    	$cacheId = md5(serialize(func_get_args()));
    	
    	if ($forceCacheUpdate // force update 
    		|| ($notSet = !isset(self::$queryCache[$cacheId]['entry'])) // no cache entry found 
    		|| ($tooOld = $this->checkAge(self::$queryCache[$cacheId]['tstamp'], $maxAge)) // entry is too old 
    		) {
    		
			if ($forceCacheUpdate) {    			
    			$updateReason = 'forced';
			} elseif ($notSet) {
				$updateReason = 'not found';
			} elseif ($tooOld) {
				$updateReason = 'too old';
			}
    		
	    	$res = $this->dbObj->exec_SELECTquery(
	    		$this->dbObj->quoteStr($select_fields, ''),
	    		$this->dbObj->quoteStr($from_table, ''),
	    		$this->dbObj->quoteStr($where_clause, ''),
	    		$this->dbObj->quoteStr($groupBy, ''),
	    		$this->dbObj->quoteStr($orderBy, ''),
	    		$this->dbObj->quoteStr($limit, '')
	    	);
	    	tx_pttools_assert::isMySQLRessource($res, $this->dbObj);
	    	
	    	self::$queryCache[$cacheId] = array(
	    		'tstamp' => time(),
	    		'entry' => $this->getRows($res, true)
	    	);

	    	// if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Updating query cache "%s" for cacheId "%s" (%s)', $this->identifier, $cacheId, $updateReason), 'pt_list', 1, self::$queryCache[$cacheId]);
    	}
    	
        return self::$queryCache[$cacheId]['entry']; 
    }
    
    
	
}

?>