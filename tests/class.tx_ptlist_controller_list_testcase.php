<?php
/***************************************************************
 *  Copyright notice
 *  
 *  (c) 2009 Michael Knoll (knoll@punkt.de)
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
require_once t3lib_extMgm::extPath('phpunit').'class.tx_phpunit_testcase.php';



/**
 * tx_ptlist_renderer test case.
 *
 * @author      Michael Knoll <knoll@punkt.de>
 * @since       2009-10-18
 * @package     TYPO3
 * @subpackage  tx_ptlist
 * @version     $Id:$
 */
class tx_ptlist_controller_list_testcase extends tx_phpunit_testcase {
    
	protected $demolistConfiguration = array();
	
	/****************************************************************
     * Set up some fake pt_list configuration 
     ****************************************************************/
	
	
	
    
    
    /****************************************************************
     * Setup Tests
     ****************************************************************/
    
    /**
     * Tests whether class-definition file is available
     */
    public function test_classdefinitionFileIsAvailable() {
        
        $this->assertTrue(
            is_file(t3lib_extMgm::extPath('pt_list').'controller/class.tx_ptlist_controller_list.php'),
            'Class definition file for tx_ptlist_controller_list is not available!'
        );
        
    }
    
    
    
    /**
     * Checks for class to be implemented
     *
     */
    public function test_classIsImplemented() {
        
        require_once(t3lib_extMgm::extPath('pt_list').'controller/class.tx_ptlist_controller_list.php');
        $this->assertTrue(class_exists('tx_ptlist_controller_list'));
        
    }

    
    
    /****************************************************************
     * Functional Tests
     ****************************************************************/
    
    
    

}

?>