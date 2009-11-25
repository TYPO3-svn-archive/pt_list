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
 * tx_ptlist_filterValue test case.
 *
 * @author      Michael Knoll <knoll@punkt.de>
 * @since       2009-10-18
 * @package     TYPO3
 * @subpackage  pt_list
 * @version     $Id:$
 */
class tx_ptlist_filterValueNumeric_testcase extends tx_phpunit_testcase {
    
	
	/**
	 * Fixture holding a test-implementation for filter-value class
	 *
	 * @var tx_ptlist_filterValue
	 */
	protected $fixture;
	
	protected function setUp() {
		
	}
    
    
    /****************************************************************
     * Setup Tests
     ****************************************************************/
    
    /**
     * Tests whether class-definition file is available
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-17
     */
    public function test_classDefinitionFileIsAvailable() {
    	
        $this->assertTrue(
            is_file(t3lib_extMgm::extPath('pt_list').'model/filter/filterValue/class.tx_ptlist_filterValueNumeric.php'),
            'Class definition file for tx_ptlist_filterValueNumeric is not available!'
        );
        
    }

    
    
    /**
     * Tests whether class is implemented
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-17
     */
    public function test_classImplementationIsAvailable() {
    	
    	require_once t3lib_extMgm::extPath('pt_list').'model/filter/filterValue/class.tx_ptlist_filterValueNumeric.php';
    	$this->assertTrue(
    	   class_exists('tx_ptlist_filterValueNumeric')
    	);
    	
    }

    
    
    /****************************************************************
     * Functional Tests
     ****************************************************************/
    
    /**
     * Tests setter for filter value
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-17
     */
    public function test_setFilterValue() {
    	
    	$this->requireFilterValueNumericClassFile();
    	$filterValueNumeric = new tx_ptlist_filterValueNumeric();
    	$filterValueNumeric->setValue('12.00');
    	
    }
    
    
    
    /**
     * Tests throwing of exception, if non-valid value is set
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-17
     */
    public function test_setNonValidFilterValue() {
    	
    	$this->setExpectedException('tx_pttools_exceptionAssertion');
    	$this->requireFilterValueNumericClassFile();
    	$filterValueNumeric = new tx_ptlist_filterValueNumeric();
    	$filterValueNumeric->setValue('test');
    	
    }
    
    
    
    /**
     * Tests getter for 'raw' (un-encoded) filter value
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-17
     */
    public function test_getRawFilterValue() {
    	
    	$this->requireFilterValueNumericClassFile();
        $filterValueNumeric = new tx_ptlist_filterValueNumeric();
    	$testValue = (float)12.00;
    	$filterValueNumeric->setValue($testValue);
    	$this->assertTrue($filterValueNumeric->getRawValue() === $testValue);
    	
    }
    
    
    
    /**
     * Tests getter for url-encoded filter value
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-17
     */
    public function test_getUrlEncodedFilterValue() {
        
        $this->requireFilterValueNumericClassFile();
        $filterValueNumeric = new tx_ptlist_filterValueNumeric();
        $testValue = (float)12.00;
        $filterValueNumeric->setValue($testValue);
        $this->assertTrue($filterValueNumeric->getUrlEncodedValue() === floatval($testValue));
        
    }
    
    
    
    /**
     * Tests getter for HTML encoded filter value
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-17
     */
    public function test_getHtmlEncodedFilterValue() {
    	
    	$this->requireFilterValueNumericClassFile();
        $filterValueNumeric = new tx_ptlist_filterValueNumeric();
        $testValue = (float)12.00;
        $filterValueNumeric->setValue($testValue);
        $this->assertTrue($filterValueNumeric->getHtmlEncodedValue() === floatval($testValue));
    	
    }
    
    
    
    /**
     * Tests getter for SQL encoded filter value
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-17
     */
    public function test_getSqlEncodedValue() {
    	
    	$this->requireFilterValueNumericClassFile();
        $filterValueNumeric = new tx_ptlist_filterValueNumeric();
        $testValue = (float)12.00;
        $filterValueNumeric->setValue($testValue);
        $this->assertTrue($filterValueNumeric->getSqlEncodedValue() === floatval($testValue));
    	
    }
    
    
    
    /****************************************************************
     * Helper methods
     ****************************************************************/
    
    /**
     * Helper for loading required class.
     * 
     * Do not put this in the file itself, as we want to test, whether file exists
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-10-21
     */
    protected function requireFilterValueNumericClassFile() {
    
        require_once t3lib_extMgm::extPath('pt_list').'model/filter/filterValue/class.tx_ptlist_filterValueNumeric.php';
        
    }
    
    

}


?>