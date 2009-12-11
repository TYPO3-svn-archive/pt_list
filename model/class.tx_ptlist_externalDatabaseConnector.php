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


require_once(PATH_t3lib.'class.t3lib_db.php');

require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_debug.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_exception.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_div.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iSingleton.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_assert.php';

require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_div.php';


/**
 * Database connector for external databases
 *
 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
 * @since		2009-02-25
 * @package     TYPO3
 * @subpackage  pt_list\model
 * @version 	$Id$
 */
class tx_ptlist_externalDatabaseConnector extends t3lib_db {
        
    protected $host = '';           // (string)
    protected $database = '';       // (string)
    protected $user = '';           // (string)
    protected $pass = '';           // (string)
    
    protected $connection = NULL;     // (resource)
    protected $selectDbResult = NULL; // (boolean) 
    
    
    /**
     * Class constructor
     *
     * @param 	string|int	dsn or uid pointing to a dsn record in tx_ptlist_databases
     * @param 	string		(optional) db initizialization string
     * @author	Fabrizio Branca <mail@fabrizio-branca.de>
     * @since	2009-03-06
     */
    public function __construct($dsn, $setDBinit=NULL) {
        $this->store_lastBuiltQuery = true;
        
        if (is_numeric($dsn)) {
        	$dsn = tx_ptlist_div::getDsnFromDbRecord($dsn);
        }
        
        $dsnArray = tx_ptlist_div::parseDSN($dsn);
        
		$this->host     = $dsnArray['hostspec'];
		$this->database = $dsnArray['database'];
		$this->user     = $dsnArray['username']; 
		$this->pass     = $dsnArray['password'];
		
		if (!is_null($setDBinit)) {
			// allow individual database intialization
        	$TYPO3setDBinit = $GLOBALS['TYPO3_CONF_VARS']['SYS']['setDBinit']; // backup original setDbInit 
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['setDBinit'] = $setDBinit;
		}
        
        // connect to database server and select database
        tx_pttools_assert::isNotEmptyString($this->host, array('message'=>'No database host found!'));
        tx_pttools_assert::isNotEmptyString($this->database, array('message'=>'No database name found!'));
        tx_pttools_assert::isNotEmptyString($this->user, array('message'=>'No database user found!'));  // note: password my be empty, this is not an error
        
        $this->connection = @$this->sql_pconnect($this->host, $this->user, $this->pass);
        tx_pttools_assert::isMySQLRessource($this->connection, $this, array('message' => 'Could not connect to database server!'));

        $this->selectDbResult = $this->sql_select_db($this->database);
        tx_pttools_assert::isTrue($this->selectDbResult, array('message' => 'Could not select database!', 'sql_error' => $this->sql_error()));
        
        if (!is_null($setDBinit)) {
			// re-set original TYPO3 database intialization if overwritten for an external GSA database
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['setDBinit'] = $TYPO3setDBinit; // perform all other connects with original TYPO3 setting
        }
    }
    
    
}

?>