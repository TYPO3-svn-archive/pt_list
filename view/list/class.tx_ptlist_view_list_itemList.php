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
 * Class definition file for default item list view
 * 
 * $ID$
 * 
 * @author Fabrizio Branca <mail@fabrizio-branca.de>
 * @since 2009-02-19
 */



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_list').'view/class.tx_ptlist_view.php';



/**
 * Class implementing standard view for item list
 * 
 * @package     TYPO3
 * @subpackage pt_list
 * @author Fabrizio Branca <mail@fabrizio-branca.de>
 * @since 2009-02-19
 */
class tx_ptlist_view_list_itemList extends tx_ptlist_view {

	/**
	 * @var bool	in typoscript mode no smarty template will be used
	 */
	protected $typoScriptMode = false;



	/**
	 * Overwrite the getTemplateFilePath to avoid searching for a template if in typoscript mode
	 *
	 * @param	void
	 * @return	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-19
	 */
	public function getTemplateFilePath() {
		if ($this->viewConf['template'] != 'none') {
			$this->typoScriptMode = false;
			parent::getTemplateFilePath();
		} else {
			$this->typoScriptMode = true;
		}
	}



	/**
	 * Overwrite the render method to make a tryposcript mode possible
	 * In typoscript mode no smarty template will be used.
	 * You decide how the list, rows and single fields will be wrapped by typoscript
	 *
	 * @param	void
	 * @return	string	HTML output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-19
	 */
	public function render() {
		
		if ($this->typoScriptMode == false) {

			/**
			 * Smarty rendering
			 */
			$output = parent::render();

		} else {

			/**
			 * Typoscript rendering
			 */

			$renderConfig = $this->viewConf['template.'];

			$list = '';
			
			$cObj = t3lib_div::makeInstance('tslib_cObj'); /* @var $cObj tslib_cObj */

			// iterate over all rows
			foreach ($this->itemsArr['listItems'] as $item) {

				$row = '';

				// iterate over all columns
				foreach ($item as $columnIdentifier => $content) {
	
					$cObj->start(array(
						'columnIdentifier' => $columnIdentifier,
						'columnContent' => $content
					));

					$field = $content;
					// if there is a columnIdentifier-specific stdWrap use this, else use "field_stdWrap"
					if (is_array($renderConfig[$columnIdentifier.'_stdWrap.'])) {
						$field = $cObj->stdWrap($field, $renderConfig[$columnIdentifier.'_stdWrap.']);
					} else {
						$field = $cObj->stdWrap($field, $renderConfig['field_stdWrap.']);
					}

					$row .= $field;
				}

				$row = $cObj->stdWrap($row, $renderConfig['row_stdWrap.']);

				$list .= $row;

			}
			$output = $cObj->stdWrap($list, $renderConfig['list_stdWrap.']);
		}

		return $output;
	}



	/**
	 * This will be executed before rendering the template
	 *
	 * @param 	void
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	public function beforeRendering() {
		parent::beforeRendering();
		$this->addItem('&###LISTPREFIX###[action]=changeSortingOrder&###LISTPREFIX###[column]=%s&###LISTPREFIX###[direction]=%s', 'additionalParamsForColumnHeaders');
	}

}

?>