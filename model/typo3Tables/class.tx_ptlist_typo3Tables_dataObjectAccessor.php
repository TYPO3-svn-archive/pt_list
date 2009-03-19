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

require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_registry.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iSingletonCollection.php';
require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_externalDatabaseConnector.php';

/**
 * Database accessor for the dataObject class
 *
 * @version 	$Id$
 * @author		Fabrizio Branca <branca@punkt.de>
 * @since		2009-01-22
 */
class tx_ptlist_typo3Tables_dataObjectAccessor implements tx_pttools_iSingletonCollection {

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


    /***************************************************************************
     *   CONSTRUCTOR & OBJECT HANDLING METHODS
     **************************************************************************/

    /**
     * Returns a unique instance (Singleton) of the object. Use this method instead of the private/protected class constructor.
	 * This implements a special type of singleton. Instead having exactly one access object, you will get a unique (butt different)
	 * accessor object per dsn.
     *
     * @param   void
     * @return  tx_ptlist_typo3Tables_dataObjectAccessor      unique instance of the object (Singleton)
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2009-01-22
     */
    public static function getInstanceById($identifier) {

		tx_pttools_assert::isNotEmptyString($identifier, array('message' => 'No "identifier" found!'));

		$className = __CLASS__;
        if (self::$uniqueInstances[$identifier] === NULL) {
			if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Creating new "%s" instance for identifier "%s"', $className, $identifier), 'pt_list');
            self::$uniqueInstances[$identifier] = new $className;
        } else {
			if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Return existing "%s" instance for identifier "%s"', $className, $identifier), 'pt_list');
		}
        return self::$uniqueInstances[$identifier];
    }



    /**
     * Final method to prevent object cloning (using 'clone'), in order to use only the unique instance of the Singleton object.
     * @param   void
     * @return  void
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2009-01-22
     */
    public final function __clone() {
        trigger_error('Clone is not allowed for '.get_class($this).' (Singleton)', E_USER_ERROR);
    }

    
    
    /**
     * This class cannot be constructed manually. Use the getInstance() method!
     * 
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2009-02-24 
     */
	private function  __construct() {
		// By default the TYPO3 database will be used. Use $this->setDatabase to switch to another one
		$this->dbObj = $GLOBALS['TYPO3_DB'];
	}



	/**
	 * Set database
	 *
	 * @param	string dsn, if equals to "<default>" the TYPO3 database will be used
	 * @return 	void
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-02-24
	 */
    public function setDatabase($dsn) {
		$this->dbObj = new tx_ptlist_externalDatabaseConnector($dsn);
		$this->externalDatabase = true;
    }


    /***************************************************************************
     *   ACCESSOR METHODS
     **************************************************************************/


	/**
	 * Get group data
	 *
	 * @param	string	list identifier
	 * @param	string	select clause
	 * @param	string	(optional) where clause
	 * @param	string	(optional) groupBy clause
	 * @param	string	(optional) orderBy clause
	 * @param	string	(optional) limit
	 * @return	array	array of record arrays
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-02-01
	 */
	public function getGroupData($listId, $select, $where='', $groupBy='', $orderBy='', $limit='') {

    	$listObject = tx_pttools_registry::getInstance()->get($listId.'_listObject'); /* @var $listObject tx_ptlist_typo3Tables_list */

    	$from 	= $listObject->getFromClause();

    	$where  .= !empty($where) ? ' AND ' : '';
        $where  .= $listObject->get_baseWhereClause();
        $where  .= $this->getEnableFields($listObject);

        $groupByClause = array();
        if ($listObject->get_baseGroupByClause()) {
        	$groupByClause[] = $listObject->get_baseGroupByClause();
        }
        if ($groupBy) {
        	$groupByClause[] = $groupBy;
        }
        $groupByClause = implode(', ',$groupByClause);

        $res = $this->dbObj->exec_SELECTquery($select, $from, $where, $groupByClause, $orderBy, $limit);
        tx_pttools_assert::isMySQLRessource($res, $this->dbObj);
        
        if (TYPO3_DLOG) t3lib_div::devLog('"getGroupData" query', 'pt_list', 1, $this->dbObj->debug_lastBuiltQuery);

        $rows = array();
        while (($a_row = $this->dbObj->sql_fetch_assoc($res)) == true) {
        	$rows[] = $a_row;
        }

        $this->dbObj->sql_free_result($res);

        return $rows;

    }



    /**
     * Select rows
     *
     * @param 	string	list identifier (this is needed to fetch additional information like table names and baseWhereClause from the list object found in the registry)
     * @param 	string	csl of fieldnames (each prefix with "[tablename|alias]." when required )
     * @param 	string	where clause (usually prepared by the filter[Collection])
     * @param 	string	order by clause (usually prepared by the columnCollection depending on the sorting states of its columns)
     * @param 	string	limit clause (usually prepared by the pager)
     * @param 	string	(optional) group by clause
     * @return 	array	array of records
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2009-01-22
     */
    public function selectRows($listId, $fields, $where, $orderBy, $limit, $groupBy='') {

		tx_pttools_assert::isNotEmptyString($fields, array('message' => 'No "fields" given!'));

		$listObject = tx_pttools_registry::getInstance()->get($listId.'_listObject'); /* @var $listObject tx_ptlist_typo3Tables_list */

    	// query preparation
        $select  = $fields;
        $from    = $listObject->getFromClause();
        $where   .= !empty($where) ? ' AND ' : '';
        $where   .= $listObject->get_baseWhereClause(); // TODO: shouldn't this go into listObjects::getItem()?

        $groupByClause = array();
        if ($listObject->get_baseGroupByClause()) {
        	$groupByClause[] = $listObject->get_baseGroupByClause();
        }
        if ($groupBy) {
        	$groupByClause[] = $groupBy;
        }
        $groupByClause = implode(', ',$groupByClause);

		if (empty($where)) $where = '1=1';
        $where   .= $this->getEnableFields($listObject);

        // exec query using TYPO3 DB API
        $res = $this->dbObj->exec_SELECTquery($select, $from, $where, $groupByClause, $orderBy, $limit);
        tx_pttools_assert::isMySQLRessource($res, $this->dbObj);

		if (TYPO3_DLOG) t3lib_div::devLog('"selectRows" query', 'pt_list', 1, $this->dbObj->debug_lastBuiltQuery);

        $rows = array();
        while (($a_row = $this->dbObj->sql_fetch_assoc($res)) == true) {
        	$rows[] = $a_row;
        }

        $this->dbObj->sql_free_result($res);

        return $rows;
    }



	/**
	 * Get enable fields
	 *
	 * @param	tx_ptlist_list	list object
	 * @return	string	mysql where snippet with restrictions from enable fields
	 * @author	Fabrizio Branca
	 * @since	2009-02-01
	 */
    protected function getEnableFields(tx_ptlist_list $listObject) {
    	$where = '';
		if (!$this->externalDatabase) {
	        foreach ($listObject->get_tables() as $table) {
				list($plainTable, $alias) = t3lib_div::trimExplode(' ', $table, true);
				if (is_array($GLOBALS['TCA'][$plainTable])) {
	        		$where .= tx_pttools_div::enableFields($plainTable, $alias);
				}
	        }
        }
        return $where;
    }



    /**
     * Count rows
     *
     * @param 	string	list identifier (this is needed to fetch additional information like table names and baseWhereClause from the list object found in the registry)
     * @param 	string	where clause (usually prepared by the filter[Collection])
     * @return 	int		quantity of rows
     * @author	Fabrizio Branca <branca@punkt.de>
     * @since	2009-01-22
     */
    public function countRows($listId, $where) {

		$listObject = tx_pttools_registry::getInstance()->get($listId.'_listObject'); /* @var $listObject tx_ptlist_typo3Tables_list */

    	// query preparation
        $select  = '1 AS dummy';
        $from    = $listObject->getFromClause();
        $groupBy = $listObject->get_baseGroupByClause();
        $where   .= !empty($where) ? ' AND ' : '';
        $where   .= $listObject->get_baseWhereClause();
		if (empty($where)) $where = '1=1';
        $where   .= $this->getEnableFields($listObject);

        // exec query using TYPO3 DB API
        $res = $this->dbObj->exec_SELECTquery($select, $from, $where, $groupBy);
        tx_pttools_assert::isMySQLRessource($res, $this->dbObj);
        
        if (TYPO3_DLOG) t3lib_div::devLog('"countRows" query', 'pt_list', 1, $this->dbObj->debug_lastBuiltQuery);
        
        $quantity = $this->dbObj->sql_num_rows($res);

        $this->dbObj->sql_free_result($res);

        return $quantity;
    }

}

?>