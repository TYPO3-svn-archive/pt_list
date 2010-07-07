<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Fabrizio Branca (mail@fabrizio-branca.de)
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
 * Collection of static methods that can be used as user function as data processor (processDataUserFunc.)
 * (Currently only implemented in tx_ptlist_controller_filter_options_group filter)
 *
 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
 * @since		2010-05-03
 */
class tx_ptlist_dataProcessor {
	
	protected static $_sortingDirection = 'asc';


	/**
	 * Split csl values into single values
	 *
	 * @param 	array 	$data
	 * @return 	array 	$data
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2010-05-03
	 */
	public static function splitCsl(array $params) {
		$tmp = array();
		foreach ($params['groupData'] as $result) {
			$singleValues = t3lib_div::trimExplode(',', $result['item'], true);
			foreach ($singleValues as $singleValue) {
				$tmp[$singleValue] += $result['quantity'];
			}
		}
		
		// rewrite array
		$newData = array();
		foreach ($tmp as $value => $quantity) {
			$newData[] = array(
				'item' => $value,
				'label' => $value,
				'quantity' => $quantity
			);
		}
		return $newData;
	}
		
	/**
	 * Lookup labels
	 * 
	 * <example>
	 *  fooFilter < plugin.tx_ptlist.alias.filter_options_group
	 *	fooFilter {
	 *		processDataUserFunc {
	 *			10 = EXT:pt_list/model/class.tx_ptlist_dataProcessor.php:tx_ptlist_dataProcessor->lookupLabels
	 *			10 {
	 *				# uid of a database connection record or complete string
	 *				# dsn = 
	 *				select {
	 *						# all select properties support stdWrap
	 *					fields = title
	 *					from = tx_foo_foo
	 *						# ###VALUE### will be replaced by the current filter value
	 *					where = uid = ###VALUE###
	 *					# groupBy = 
	 *					# orderBy =
	 *				}	
	 *				# renderObj = 
	 *				# renderUserFunctions =
	 *			}
	 *		} 
	 *	}
	 * </example>
	 *
	 * @param 	array 	$data
	 * @return 	array 	$data
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2010-05-03
	 */
	public static function lookupLabels(array $params) {
		
		$conf = $params['conf'];
		$dsn = $conf['dsn'];

		if (is_numeric($dsn)) {
			$dsn = tx_ptlist_div::getDsnFromDbRecord($dsn);
		}
		
		tx_pttools_assert::isNotEmptyArray($conf['select.'], array('message' => 'No "select" configuration found!'));
		
		foreach ($params['groupData'] as &$data) { /* @var $data array */
			
			// get a fresh copy of the select statement
			$select = $conf['select.'];

			// process stdWraps and insert dynamic values
			$replaceArray = array('###VALUE###' => $data['item']);
			foreach ($select as $tsKey => &$value) {
				if (substr($tsKey, -1) != '.') {
					$value = $GLOBALS['TSFE']->cObj->stdWrap($select[$tsKey], $select[$tsKey.'.']);
					$value = str_replace(array_keys($replaceArray), array_values($replaceArray), $value);
				}
			}
	
			// get data from database
			list($row) = tx_ptlist_genericDataAccessor::getInstance(md5($dsn), $dsn)->select(
				$select['fields'],
				$select['from'],
				$select['where'],
				$select['groupBy'],
				$select['orderBy'],
				1, // $conf['select.']['limit'],
				false, // force update
				0 // max age
			);
	
			// render data
			if (!empty($row)) {
				$renderConfig = array(
					'renderObj' => $conf['renderObj'],
					'renderObj.' => $conf['renderObj.'],
					'renderUserFunctions.' => $conf['renderUserFunctions.'],
				);
				$data['label'] = tx_ptlist_div::renderValues($row, $renderConfig);
			}
		}
		
		return $params['groupData'];
	}
	
	
		
	/**
	 * Order data by label
	 *
	 * @param 	array 	$data
	 * @return 	array 	$data
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2010-05-03
	 */
	public static function orderByLabel(array $params) {
		if ($params['conf']['desc']) {
			self::$_sortingDirection = 'desc';
		} else {
			self::$_sortingDirection = 'asc';
		}
		uasort($params['groupData'], array(self, 'labelSorter'));
		
		return $params['groupData'];
	}
	
	
	
    /**
     * Generic field sorter used as callback function for usort in the sort_ "magic method"
     * 
     * @param tx_tcaobjects_object $a
     * @param tx_tcaobjects_object $b
     * @return int -1, 0, 1
     * @author Fabrizio Branca <mail@fabrizio-branca.de>
     */
    protected function labelSorter(array $a, array $b) {
    	$res = ($a['label'] > $b['label']) ? +1 : -1;
    	if (self::$_sortingDirection == 'desc') {
    		$res *= -1;
    	}
    	return $res;
    }


}

?>