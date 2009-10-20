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

require_once t3lib_extMgm::extPath('pt_list').'model/filter/filterValue/class.tx_ptlist_filterValue.php';


/***************************************************************************
 * Test-Implementation of abstract tx_ptlist_filterValue used for testing
 ***************************************************************************/

class tx_ptlist_filterValue_testcase_implementation extends tx_ptlist_filterValue {
    
    public function getUrlEncodedValue() {
        return null;
    }
    
    public function getHtmlEncodedValue() {
        return null;
    }
    
    public function getSqlEncodedValue() {
        return null;
    }
	
}


/**
 * tx_ptlist_filterValue test case.
 *
 * @author      Michael Knoll <knoll@punkt.de>
 * @since       2009-10-18
 * @package     TYPO3
 * @subpackage  pt_list
 * @version     $Id:$
 */
class tx_ptlist_filterValue_testcase extends tx_phpunit_testcase {
    
	
	/**
	 * Fixture holding a test-implementation for filter-value class
	 *
	 * @var tx_ptlist_filterValue
	 */
	protected $fixture;
	
	protected function setUp() {
		$this->fixture = new tx_ptlist_filterValue_testcase_implementation();
	}
    
    
    /****************************************************************
     * Setup Tests
     ****************************************************************/
    
    /**
     * Tests whether class-definition file is available
     */
    public function test_classDefinitionFileIsAvailable() {
        $this->assertTrue(
            is_file(t3lib_extMgm::extPath('pt_list').'model/filter/filterValue/class.tx_ptlist_filterValue.php'),
            'Class definition file for filterValue is not available!'
        );
        
    }

    
    
    /****************************************************************
     * Functional Tests
     ****************************************************************/
    
    public function test_setFilterValue() {
    	
    	$this->fixture->setValue('test');
    	
    }
    
    
    
    public function test_getRawFilterValue() {
    	
    	$testValue = '&1234<>?';
    	$this->fixture->setValue($testValue);
    	$this->assertTrue($this->fixture->getRawValue() == $testValue);
    	
    }

    

}

?>