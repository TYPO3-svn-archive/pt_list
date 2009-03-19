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

require_once t3lib_extMgm::extPath('pt_list').'controller/filter/options/class.tx_ptlist_controller_filter_options_base.php';


/**
 * Group filter class
 *
 * @version 	$Id$
 * @author		Fabrizio Branca <branca@punkt.de>
 * @since		2009-01-23
 */
class tx_ptlist_controller_filter_options_explicit extends tx_ptlist_controller_filter_options_base {

	/**
	 * Get options for this filter
	 *
	 * @param 	void
	 * @return 	array	array of array('item' => <value>, 'label' => <label>, 'quantity' => <quantity>)
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-02-21
	 */
	public function getOptions() {

		tx_pttools_assert::isNotEmptyArray($this->conf['options.'], array('message' => 'No options defined!'));

		$options = array();
		
		// first sort array by keys (as we expect single option definitions defined under keys 10., 20., 30., ...)
		$sortedKeys = t3lib_TStemplate::sortedKeyList($this->conf['options.'], true);
			
		foreach ($sortedKeys as $tsKey) {
			$option = $this->conf['options.'][$tsKey];
			tx_pttools_assert::isNotEmptyArray($option, array(sprintf('Inalid option found in key "%s"', $tsKey)));
			
			$options[$tsKey] = array(
				'item' => 		$option['item'],
				'label' => 		$GLOBALS['TSFE']->sL($option['label']),
				'quantity' => 	$option['quantity'],
				'class' => 		$option['class'],
			);
		}
				
		return $options;
	}

}

?>