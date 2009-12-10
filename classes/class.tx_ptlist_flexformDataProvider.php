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


/**
 * Class 'tx_ptlist_flexformDataProvider'
 * 
 * $Id$
 *
 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
 * @since   2008-02-06
 */	
class tx_ptlist_flexformDataProvider {
    
	
	/**
	 * Get available list classes registred by an hook
	 *
	 * @param	array	configuration array
	 * @return 	array	configuration array
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
    public function getAvailableListClasses(array $config) {
		
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][t3lib_extMgm::extPath('pt_list').'classes/class.tx_ptlist_flexformDataProvider.php']['availableListClasses'])){
			foreach($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][t3lib_extMgm::extPath('pt_list').'classes/class.tx_ptlist_flexformDataProvider.php']['availableListClasses'] as $listObjectsArray){
				$config['items'][] = array ($GLOBALS['LANG']->sL($listObjectsArray['name']), $listObjectsArray['path']);
			}
		}
        return $config;
	}
    
	
	/**
	 * Get available pager strategy classes registred by an hook
	 *
	 * @param	array	configuration array
	 * @return 	array	configuration array
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-27
	 */
    public function getAvailablePagerStrategyClasses(array $config) {
		
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][t3lib_extMgm::extPath('pt_list').'classes/class.tx_ptlist_flexformDataProvider.php']['availablePagerStrategyClasses'])){
			foreach($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][t3lib_extMgm::extPath('pt_list').'classes/class.tx_ptlist_flexformDataProvider.php']['availablePagerStrategyClasses'] as $listObjectsArray){
				$config['items'][] = array ($GLOBALS['LANG']->sL($listObjectsArray['name']), $listObjectsArray['path']);
			}
		}
        return $config;
	}
}

?>