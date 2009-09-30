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

require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_filter.php';
require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_genericDataAccessor.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_assert.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_registry.php';



/**
 * Collection of static methods
 *
 * @version 	$Id$
 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
 * @since		2009-02-23
 * @package     TYPO3
 * @subpackage  tx_ptlist
 */
class tx_ptlist_div {
	
	protected static $renderCache = array();

	
	
	/**
	 * Redirects on validate, by setting the "forcedNextAction" of the filter's listObject to the redirect action
	 *
	 * @global 	TSFE	uses the TSFE's cObj for url generation
	 * @param 	array 	parameter array, expects "$params['conf']['target']" to hold the target where to redirect to
	 * @param 	tx_ptlist_filter 	calling filter object
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>, Rainer Kuhn <kuhn@punkt.de>
	 * @since	2009-02-23
	 */
	public static function redirectOnValidate(array $params, tx_ptlist_filter $filterObj) {
	    
		tx_pttools_assert::isNotEmptyString($params['conf']['target'], array('message' => 'No "target" found for redirect!'));
		if ($params['conf']['urlParameters'] || $params['conf']['urlParameters.']) {
		    tx_pttools_assert::isArray($params['conf']['urlParameters.'], array('message'=>'No URL params array given for redirect!'));
		} else {
		    $params['conf']['urlParameters.'] = array();
		}
		
		// next action: redirect to target page
        $listControllerObj = tx_pttools_registry::getInstance()->get($filterObj->get_listIdentifier().'_listControllerObject');
		$listControllerObj->set_forcedNextAction('redirect', array('target' => $GLOBALS['TSFE']->cObj->getTypoLink_URL($params['conf']['target'], $params['conf']['urlParameters.'])));
		
	}
	
	
	
	/**
     * 
     *
     * @param   void
     * @return  void
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009
     */
	public function hookEofe() {
	    
		if (TYPO3_DLOG) t3lib_div::devLog('Processing tslib_fe hook "hook_eofe" in '.__METHOD__, 'pt_list', 1);
		tx_ptlist_genericDataAccessor::storeAllCachesToSession();
		
	}

	

	/**
	 * Renders an array of values for a given configuration via php userfunction or TYPO3 content object and stores it into an own cache
	 * TODO: move this to a library (pt_tools?)
	 *
	 * @example
	 * <code>
	 * $config = array(
	 * 		'rendererUserFunctions.' => array(
	 * 			'10' => 'EXT:my_ext/classes/class.tx_myext_div.php:tx_myext_div>myUserFunction',
	 * 			'10.' => array(), // this array will be passed to the userfunction in the params array in the key "conf"
	 * 			'20' => [...]
	 * 		),
	 * 		'renderObj' => 'TEXT'
	 * 		'renderObj.' => array(
	 * 			'field' => '<oneOfTheKeysInTheValuesArray>',
	 * 			'wrap' => '<span>|</span>',
	 * 		),
	 * );
	 * </code>
	 * @param 	array	values to be rendered
	 * @param 	array	(optional) key "renderUserFunctions." may contain an array of userfunctions, keys "renderObj" and "renderObj." contain the configuration for the content object
	 * @param 	array	(optional) if true the content will be rendered even if there is an entry in the cache. The rendered value will be stored in the cache
	 * @return 	string 	rendered content
	 * @author 	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-21
	 */
	public static function renderValues(array $values, array $config=array(), $forceCacheUpdate=false) {
		
		$cacheId = md5(serialize(func_get_args()));
		
		if ($forceCacheUpdate || !isset(self::$renderCache[$cacheId])) {
		
			// values will be concatenated with ", " by default
			$renderedContent = implode(', ', $values);
			
			if (!empty($config)) {
		
				// apply (php) renderer to field Content
				if (is_array($config['renderUserFunctions.'])) {
					
					$sortedKeys = t3lib_TStemplate::sortedKeyList($config['renderUserFunctions.'], false);
					
					$params = array();
					$params['values'] = $values;
					
					$dummRef = ''; // as this method is called statically we create a dummy variable that will be passed to the user function
					
					foreach ($sortedKeys as $key) {
						$rendererUserFunc = $config['renderUserFunctions.'][$key];
						$params['currentContent'] = $renderedContent;
						$params['conf'] = $config['renderUserFunctions.'][$key.'.']; // pass the configuration found under "<key>." to the userfunction
						$renderedContent = t3lib_div::callUserFunction($rendererUserFunc, $params, $dummRef);
					}
					
				}
		
				// render typoscript cObj if defined
				if (!empty($config['renderObj.'])) {
		
					$local_cObj = t3lib_div::makeInstance('tslib_cObj');
					$local_cObj->start($values);
		
					$config['renderObj.']['setCurrent'] = $renderedContent;
					// $renderedContent = $GLOBALS['TSFE']->cObj->cObjGetSingle($this->cObj['name'], $this->cObj['conf']);
					$renderedContent = $local_cObj->cObjGetSingle($config['renderObj'], $config['renderObj.']);
				}
			}
			self::$renderCache[$cacheId] = $renderedContent;
		}

		return self::$renderCache[$cacheId];
	}
	

    
    /**
     * Reads a database record from the TYPO3 database where dsn information for the external database
     * is stored and constructs a dsn
     *
     * @param 	int		uid of the database record
     * @return 	string 	dsn
     * @author	Fabrizio Branca <mail@fabrizio-branca.de>
     * @since	2009-03-06
     */
    public static function getDsnFromDbRecord($uid) {
    	tx_pttools_assert::isValidUid($uid, false, array('message' => 'No valid uid!'));
    	$record = $GLOBALS['TSFE']->sys_page->checkRecord('tx_ptlist_databases', $uid, 0);
    	return sprintf('mysql://%s:%s@%s/%s', $record['username'], $record['pass'], $record['host'], $record['db']);
    }
    
    
    
    /**
     * Parses a dsn string into an array
     * 
 	 * phptype(dbsyntax)://username:password@protocol+hostspec/database?option=8&another=true
 	 * 
     * @see http://euk1.php.net/package/DB/docs/latest/DB/DB.html#methodparseDSN
     * @param 	string	dsn string
     * @return 	array	parsed string
     * @author	Fabrizio Branca <mail@fabrizio-branca.de>
     * @since	2009-03-06
     */
    public static function parseDSN($dsn){
        $parsed = array(
            'phptype'  => false,
            'dbsyntax' => false,
            'username' => false,
            'password' => false,
            'protocol' => false,
            'hostspec' => false,
            'port'     => false,
            'socket'   => false,
            'database' => false,
        );

        if (is_array($dsn)) {
            $dsn = array_merge($parsed, $dsn);
            if (!$dsn['dbsyntax']) {
                $dsn['dbsyntax'] = $dsn['phptype'];
            }
            return $dsn;
        }

        // Find phptype and dbsyntax
        if (($pos = strpos($dsn, '://')) !== false) {
            $str = substr($dsn, 0, $pos);
            $dsn = substr($dsn, $pos + 3);
        } else {
            $str = $dsn;
            $dsn = null;
        }

        // Get phptype and dbsyntax
        // $str => phptype(dbsyntax)
        $arr = array();
        if (preg_match('|^(.+?)\((.*?)\)$|', $str, $arr)) {
            $parsed['phptype']  = $arr[1];
            $parsed['dbsyntax'] = !$arr[2] ? $arr[1] : $arr[2];
        } else {
            $parsed['phptype']  = $str;
            $parsed['dbsyntax'] = $str;
        }

        if (!count($dsn)) {
            return $parsed;
        }

        // Get (if found): username and password
        // $dsn => username:password@protocol+hostspec/database
        if (($at = strrpos($dsn,'@')) !== false) {
            $str = substr($dsn, 0, $at);
            $dsn = substr($dsn, $at + 1);
            if (($pos = strpos($str, ':')) !== false) {
                $parsed['username'] = rawurldecode(substr($str, 0, $pos));
                $parsed['password'] = rawurldecode(substr($str, $pos + 1));
            } else {
                $parsed['username'] = rawurldecode($str);
            }
        }

        // Find protocol and hostspec
        $match = array();
        if (preg_match('|^([^(]+)\((.*?)\)/?(.*?)$|', $dsn, $match)) {
            // $dsn => proto(proto_opts)/database
            $proto       = $match[1];
            $proto_opts  = $match[2] ? $match[2] : false;
            $dsn         = $match[3];

        } else {
            // $dsn => protocol+hostspec/database (old format)
            if (strpos($dsn, '+') !== false) {
                list($proto, $dsn) = explode('+', $dsn, 2);
            }
            if (strpos($dsn, '/') !== false) {
                list($proto_opts, $dsn) = explode('/', $dsn, 2);
            } else {
                $proto_opts = $dsn;
                $dsn = null;
            }
        }

        // process the different protocol options
        $parsed['protocol'] = (!empty($proto)) ? $proto : 'tcp';
        $proto_opts = rawurldecode($proto_opts);
        if (strpos($proto_opts, ':') !== false) {
            list($proto_opts, $parsed['port']) = explode(':', $proto_opts);
        }
        if ($parsed['protocol'] == 'tcp') {
            $parsed['hostspec'] = $proto_opts;
        } elseif ($parsed['protocol'] == 'unix') {
            $parsed['socket'] = $proto_opts;
        }

        // Get dabase if any
        // $dsn => database
        if ($dsn) {
            if (($pos = strpos($dsn, '?')) === false) {
                // /database
                $parsed['database'] = rawurldecode($dsn);
            } else {
                // /database?param1=value1&param2=value2
                $parsed['database'] = rawurldecode(substr($dsn, 0, $pos));
                $dsn = substr($dsn, $pos + 1);
                if (strpos($dsn, '&') !== false) {
                    $opts = explode('&', $dsn);
                } else { // database?param1=value1
                    $opts = array($dsn);
                }
                foreach ($opts as $opt) {
                    list($key, $value) = explode('=', $opt);
                    if (!isset($parsed[$key])) {
                        // don't allow params overwrite
                        $parsed[$key] = rawurldecode($value);
                    }
                }
            }
        }

        return $parsed;
    }
    

}

?>