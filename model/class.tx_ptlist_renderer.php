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

require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_genericDataAccessor.php';

/**
 * Collection of static methods that can be used as user function as data renderer (renderUserFunctions.)
 *
 * @version 	$Id$
 * @author		Fabrizio Branca <branca@punkt.de>
 * @since		2009-02-10
 */
class tx_ptlist_renderer {
	
	
	/**
	 * Process content with a cObject
	 *
	 * @param 	array 	$params
	 * @return 	string 	rendered content
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-03-06
	 */
	public static function cObject(array $params) {
		$local_cObj = t3lib_div::makeInstance('tslib_cObj');
		$local_cObj->data = $params['values'];
		return $local_cObj->cObjGetSingle($params['conf']['renderObj'], $params['conf']['renderObj.']);
	}
	
	
	
	/**
	 * Display edit icon
	 *
	 * @param 	array 	params
	 * @return 	string 	rendered content
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-03-06
	 */
	public static function editIcon(array $params) {
		$values = $params['values'];
		$conf = $params['conf'];
		$currentContent = $params['currentContent'];
		
		if ($GLOBALS['TSFE']->beUserLogin > 0) {
			
			// $row = t3lib_BEfunc::getRecord('static_countries', $values['countryuid']);
			$row = array('uid' => $values[$conf['dataDescriptionIdentifierContainingTheUid']]);

			// force edit icons to be displayed
			$backupDisplayFieldEditIcons = $GLOBALS['TSFE']->displayFieldEditIcons;
			$GLOBALS['TSFE']->displayFieldEditIcons = true;
			
			$currentContent = $GLOBALS['TSFE']->cObj->editIcons(
				$currentContent,
				$conf['table'].':'.$conf['fields'],
				$conf['editIconConf.'],
				$conf['table'].':'.$row['uid'],
				$row,
				'&viewUrl='.rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI'))
			);
			
			// restore original displayFieldEditIcons status
			$GLOBALS['TSFE']->displayFieldEditIcons = $backupDisplayFieldEditIcons;
		}
		return $currentContent;
	}

	
	
	/**
	 * Regex replace
	 *
	 * @param 	array 	params
	 * @return 	string 	rendered content
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-03-06
	 */
	public static function regexReplace(array $params) {
		return preg_replace($params['conf']['pattern'], $params['conf']['replace'], $params['currentContent']);
	}
	
	
	
	/**
	 * Fetch external data and render it
	 * For fetching the external data the genericDataAccessor is used which uses a query cache and stores it even to the session
	 *
	 * @param 	array 	params
	 * @return 	string 	rendered content
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-02-25
	 */
	public static function fetchExternalData(array $params) {
				
		$values = $params['values'];
		$conf = $params['conf'];
		// $currentContent = $params['currentContent'];
			
		$dsn = $conf['dsn'];
		
		if (is_numeric($dsn)) {
			$dsn = tx_ptlist_div::getDsnFromDbRecord($dsn);
		}
		
		$replaceArray = array();
		foreach ($values as $key => $value) {
			$replaceArray['###'.strtoupper($key).'###'] = $value;
		}
		
		tx_pttools_assert::isNotEmptyArray($conf['select.'], array('message' => 'No "select" configuration found!'));
		
		foreach ($conf['select.'] as $tsKey => &$value) {
			if (substr($tsKey, -1) != '.') {
				
				// resolve stdWraps
				$value = $GLOBALS['TSFE']->cObj->stdWrap($conf['select.'][$tsKey], $conf['select.'][$tsKey.'.']);
				
				// insert values
				$value = str_replace(array_keys($replaceArray), array_values($replaceArray), $value);
			}
		}
		
		$row = tx_ptlist_genericDataAccessor::getInstance(md5($dsn), $dsn)->select(
			$conf['select.']['fields'],
			$conf['select.']['from'],
			$conf['select.']['where'],
			$conf['select.']['groupBy'],
			$conf['select.']['orderBy'],
			1, // $conf['select.']['limit'],
			false, // force update
			0 // max age
		);
		
		if (!empty($row[0])) {
			// merge existing values with the values from the row
			$values = t3lib_div::array_merge($values, $row[0]);
		}
		
		$renderConfig = array(
			'renderObj' => $conf['renderObj'],
			'renderObj.' => $conf['renderObj.'],
			'renderUserFunctions.' => $conf['renderUserFunctions.'],
		);
		return tx_ptlist_div::renderValues($values, $renderConfig);
	}
	
	
}

?>