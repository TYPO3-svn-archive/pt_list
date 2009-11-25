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
 * @since       2009-11-18
 * @package     TYPO3
 * @subpackage  pt_list
 * @version     $Id:$
 */
class tx_ptlist_filterValueDate_testcase extends tx_phpunit_testcase {
	
	/**
	 * Fixture holding a test-implementation for filter-value class
	 *
	 * @var tx_ptlist_filterValue
	 */
	protected $fixture;
	
	
	
	/**
	 * No setup to be done so far
	 *
	 */
	protected function setUp() {
		
	}
    
    
    /****************************************************************
     * Setup Tests
     ****************************************************************/
    
    /**
     * Tests whether class-definition file is available
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-18
     */
    public function test_classDefinitionFileIsAvailable() {
        $this->assertTrue(
            is_file(t3lib_extMgm::extPath('pt_list').'model/filter/filterValue/class.tx_ptlist_filterValueDate.php'),
            'Class definition file for tx_ptlist_filterValueDate is not available!'
        );
    }

    
    
    /**
     * Tests whether class is implemented
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-18
     */
    public function test_classImplementationIsAvailable() {
    	require_once t3lib_extMgm::extPath('pt_list').'model/filter/filterValue/class.tx_ptlist_filterValueDate.php';
    	$this->assertTrue(
    	   class_exists('tx_ptlist_filterValueDate'),
    	   'tx_ptlist_filterValueDate class does not exist!'
    	);
    }
    
    
    /**
     * Tests availability and content of constants
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-24
     */
    public function test_constants() {
        $this->requirefilterValueDateClassFile();
        $this->assertEquals(tx_ptlist_filterValueDate::DD_DOT_MM_DOT_YYYY_INPUT_FORMAT, 'd.m.Y');
        $this->assertEquals(tx_ptlist_filterValueDate::DD_DOT_MM_DOT_YY_INPUT_FORMAT, 'd.m.y');
    }

    
    
    /****************************************************************
     * Functional Tests
     ****************************************************************/
    
    /**
     * Tests setter for filter value
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-18
     */
    public function test_setFilterWithFormatValue() {
    	$this->requirefilterValueDateClassFile();
    	$filterValueDate = new tx_ptlist_filterValueDate();
    	$filterValueDate->setValueWithFormat('21.12.2009', 'd.m.Y');
    }
    
    
    
    /**
     * Tests throwing of exception, if non-valid value is set
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-18
     */
    public function test_setNonValidFilterValue() {
    	$this->setExpectedException('tx_pttools_exceptionAssertion');
    	$this->requirefilterValueDateClassFile();
    	$filterValueDate = new tx_ptlist_filterValueDate();
    	$filterValueDate->setValue('test');
    }
    
    
    
    /**
     * Tests getter for 'raw' (un-encoded) filter value
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-18
     */
    public function test_getRawFilterValue() {
    	$this->requirefilterValueDateClassFile();
        $filterValueDate = new tx_ptlist_filterValueDate();
    	$testValue = '21.12.2009';
    	$filterValueDate->setValue($testValue);
    	$this->assertTrue($filterValueDate->getRawValue() === $testValue);
    }
    
    
    
    /**
     * Tests getter for url-encoded filter value
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-18
     */
    public function test_getUrlEncodedFilterValue() {
        $this->requirefilterValueDateClassFile();
        $filterValueDate = new tx_ptlist_filterValueDate();
        $testValue = '21.12.2009';
        $filterValueDate->setValue($testValue);
        $this->assertEquals($filterValueDate->getUrlEncodedValue(), urlencode('21.12.2009'));
    }
    
    
    
    /**
     * Tests getter for HTML encoded filter value
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-18
     */
    public function test_getHtmlEncodedFilterValue() {
    	$this->requirefilterValueDateClassFile();
        $filterValueDate = new tx_ptlist_filterValueDate();
        $testValue = '21.12.2009';
        $filterValueDate->setValue($testValue);
        $this->assertTrue($filterValueDate->getHtmlEncodedValue() === htmlspecialchars($testValue));
    }
    
    
    
    /**
     * Tests getter for SQL encoded filter value
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-18
     */
    public function test_getSqlEncodedValue() {
    	$this->requirefilterValueDateClassFile();
        $filterValueDate = new tx_ptlist_filterValueDate();
        $testValue = '21.12.2009';
        $filterValueDate->setValue($testValue);
        $this->assertEquals($filterValueDate->getSqlEncodedValue(), $GLOBALS['TYPO3_DB']->fullQuoteStr($testValue,''));
    }
    
    
    
    /**
     * Tests parsing of date parts for 'd.m.Y' date format
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-24
     */
    public function test_dmYParsing() {
    	$this->requirefilterValueDateClassFile();
    	$filterValueDate = new tx_ptlist_filterValueDate();
    	$testValue = '21.12.2009';
    	$filterValueDate->setValueWithFormat($testValue, tx_ptlist_filterValueDate::DD_DOT_MM_DOT_YYYY_INPUT_FORMAT);
    	$parsedDatePartArray = $filterValueDate->getParsedValuesArray();
    	$this->assertTrue($parsedDatePartArray['d'] == '21');
    	$this->assertTrue($parsedDatePartArray['m'] == '12');
    	$this->assertTrue($parsedDatePartArray['Y'] == '2009');
    }
    
    
    
    /**
     * Tests parsing of date parts for 'Y-m-d' date format
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-24
     */
    public function test_YmdParsing() {
        $this->requirefilterValueDateClassFile();
        $filterValueDate = new tx_ptlist_filterValueDate();
        $testValue = '2009-12-21';
        $filterValueDate->setValueWithFormat($testValue, tx_ptlist_filterValueDate::YYYY_DASH_MM_DASH_DD_INPUT_FORMAT);
        $parsedDatePartArray = $filterValueDate->getParsedValuesArray();
        $this->assertTrue($parsedDatePartArray['d'] == '21');
        $this->assertTrue($parsedDatePartArray['m'] == '12');
        $this->assertTrue($parsedDatePartArray['Y'] == '2009');
    }
    
    
    
    /**
     * Tests direct access to date object via date_format
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-11-24
     */
    public function test_getValueByFormat() {
        $this->requirefilterValueDateClassFile();
        $filterValueDate = new tx_ptlist_filterValueDate();
        $testValue = '2009-12-21';
        $filterValueDate->setValueWithFormat($testValue, tx_ptlist_filterValueDate::YYYY_DASH_MM_DASH_DD_INPUT_FORMAT);
        $this->assertEquals($filterValueDate->getValueByFormat('d'),'21');
        $this->assertEquals($filterValueDate->getValueByFormat('m'), '12');
        $this->assertEquals($filterValueDate->getValueByFormat('Y'), '2009');
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
    protected function requirefilterValueDateClassFile() {
        require_once t3lib_extMgm::extPath('pt_list').'model/filter/filterValue/class.tx_ptlist_filterValueDate.php';
    }
    
}


?>