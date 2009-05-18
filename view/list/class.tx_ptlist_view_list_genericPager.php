<?php



/***************************************************************
*  Copyright notice
*
*  (c) 2008 Michael Knoll (knoll@punkt.de>
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
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_mvc').'classes/class.tx_ptmvc_view.php';



/**
 * Class for generic pager view
 * 
 * @package     TYPO3
 * @subpackage  pt_list
 * @since       2009-05-07
 * @author      Michael Knoll <knoll@punkt.de>
 */
class tx_ptlist_view_list_genericPager extends tx_ptmvc_view {
	
	
	
	/**
	 * @var string     Collection prefix
	 */
	protected $collectionPrefix;
	
	
	
    /**
     * Class constructor
     *
     * @param   object  (optional) reference to the calling object
     * @param   string  (optional) collection prefix (for multiple pagers on the same page)
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-05-07
     */
    public function __construct($controller, $collectionPrefix='') {
    	
    	$this->collectionPrefix = $collectionPrefix;
        parent::__construct($controller);
        tx_pttools_assert::isInstanceOf($this->controller, 'tx_ptmvc_controller', array('message' => 'This view expects a "tx_ptmvc_controller" registered to the view object'));
        
    }
    
    
    
    /**
     * Before rendering method.
     * Makes additional items available in the template
     * 
     * @param   void
     * @return  void
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-05-07
     */
    public function beforeRendering() {
    	
        $this->addItem($this->collectionPrefix, 'listPrefix');
        
    }
    
    
    
    /**
     * After rendering method.
     * Replaces some extra markers in the rendered content
     *
     * @param   string  rendered content
     * @return  string  rendered content
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-05-07
     */
    public function afterRendering($output) {
        
        $replace['###LISTPREFIX###'] = $this->collectionPrefix;
        
        $output = str_replace(array_keys($replace), array_values($replace), $output);
        return $output;
        
    }
    
    
	
}

?>