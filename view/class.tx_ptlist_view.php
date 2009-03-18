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

require_once t3lib_extMgm::extPath('pt_mvc').'classes/class.tx_ptmvc_view.php';

/**
 * Base class for view in the pt_list extension
 * 
 * @version 	$Id: class.tx_ptlist_view.php,v 1.4 2009/02/13 13:26:39 ry44 Exp $
 * @author		Fabrizio Branca <branca@punkt.de>
 * @since		2009-01-22
 */
class tx_ptlist_view extends tx_ptmvc_view {
	
	/**
	 * Class constructor
	 *
	 * @param 	object	(optional) reference to the calling object
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-01-22
	 */
	public function __construct($controller=NULL) {
		parent::__construct($controller);
		tx_pttools_assert::isInstanceOf($this->controller, 'tx_ptlist_controller_list', array('message' => 'This view expects a "tx_ptlist_controller_list" registered to the view object'));
	}
	
	
	
	/**
	 * Before rendering method.
	 * Makes additional items available in the template
	 * 
	 * @param 	void
	 * @return 	void
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-01-22
	 */
	public function beforeRendering() {
		$this->addItem($this->controller->get_listPrefix(), 'listPrefix');
	}
	
	
	
	/**
	 * After rendering method.
	 * Replaces some extra markers in the rendered content
	 *
	 * @param 	string 	rendered content
	 * @return 	string 	rendered content
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-01-22
	 */
	public function afterRendering($output) {
		
		$replace['###LISTPREFIX###'] = $this->controller->get_listPrefix();
        
        $output = str_replace(array_keys($replace), array_values($replace), $output);
		return $output;
	}
	
}

?>