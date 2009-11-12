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
class tx_ptlist_filterValueString_testcase extends tx_phpunit_testcase {
    
	
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
     * @since  2009-10-21
     */
    public function test_classDefinitionFileIsAvailable() {
    	
        $this->assertTrue(
            is_file(t3lib_extMgm::extPath('pt_list').'model/filter/filterValue/class.tx_ptlist_filterValueString.php'),
            'Class definition file for tx_ptlist_filterValueString is not available!'
        );
        
    }

    
    
    /**
     * Tests whether class is implemented
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-10-21
     */
    public function test_classImplementationIsAvailable() {
    	
    	require_once t3lib_extMgm::extPath('pt_list').'model/filter/filterValue/class.tx_ptlist_filterValueString.php';
    	$this->assertTrue(
    	   class_exists('tx_ptlist_filterValueString')
    	);
    	
    }

    
    
    /****************************************************************
     * Functional Tests
     ****************************************************************/
    
    /**
     * Tests setter for filter value
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-10-21
     */
    public function test_setFilterValue() {
    	
    	$this->requireFilterValueStringClassFile();
    	$filterValueString = new tx_ptlist_filterValueString();
    	$filterValueString->setValue('test');
    	
    }
    
    
    
    /**
     * Tests getter for 'raw' (un-encoded) filter value
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-10-21
     */
    public function test_getRawFilterValue() {
    	
    	$this->requireFilterValueStringClassFile();
        $filterValueString = new tx_ptlist_filterValueString();
    	$testValue = '&1234<>?';
    	$filterValueString->setValue($testValue);
    	$this->assertTrue($filterValueString->getRawValue() == $testValue);
    	
    }
    
    
    
    /**
     * Tests getter for url-encoded filter value
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-10-21
     */
    public function test_getUrlEncodedFilterValue() {
        
        $this->requireFilterValueStringClassFile();
        $filterValueString = new tx_ptlist_filterValueString();
        $testValue = '&1234<>?';
        $filterValueString->setValue($testValue);
        $this->assertTrue($filterValueString->getUrlEncodedValue() == urlencode($testValue));
        
    }
    
    
    
    /**
     * Tests getter for HTML encoded filter value
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-10-21
     */
    public function test_getHtmlEncodedFilterValue() {
    	
    	$this->requireFilterValueStringClassFile();
        $filterValueString = new tx_ptlist_filterValueString();
        $testValue = '&1234<>?';
        $filterValueString->setValue($testValue);
        $this->assertTrue($filterValueString->getHtmlEncodedValue() == htmlspecialchars($testValue));
    	
    }
    
    
    
    /**
     * Tests getter for SQL encoded filter value
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-10-21
     */
    public function test_getSqlEncodedValue() {
    	
    	$this->requireFilterValueStringClassFile();
        $filterValueString = new tx_ptlist_filterValueString();
        $testValue = '"; DELETE FROM *;';
        $filterValueString->setValue($testValue);
        $this->assertTrue($filterValueString->getSqlEncodedValue() == $GLOBALS['TYPO3_DB']->fullQuoteStr($testValue,''));
    	
    }
    
    
    
    /**
     * Tests getter for splitted and SQL encoded filter value
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-12
     */
    public function test_getSplittedByValueSqlEncoded() {
    	
    	$this->requireFilterValueStringClassFile();
    	$filterValueString = new tx_ptlist_filterValueString();
    	$value = 'test, "; DELETE FROM *;,test2';
    	$filterValueString->setValue($value);
    	$escapedValues = $filterValueString->getSplittedByValueSqlEncoded();
    	$expectedEncodedValues = array(
    	   "'test'",
    	   "' \\\"; DELETE FROM *;'",
    	   "'test2'"
    	);
    	$this->assertTrue($escapedValues == $expectedEncodedValues);
    	
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
    protected function requireFilterValueStringClassFile() {
    
        require_once t3lib_extMgm::extPath('pt_list').'model/filter/filterValue/class.tx_ptlist_filterValueString.php';
        
    }
    
    

}


?>