<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Simon Schaufelberger (schaufelberger@punkt.de)
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

require_once t3lib_extMgm::extPath('pt_list').'classes/class.tx_ptlist_dateX.php';

require_once t3lib_extMgm::extPath('phpunit').'class.tx_phpunit_testcase.php';

/**
 * tx_ptlist_dateX test case.
 *
 * @author		Simon Schaufelberger (schaufelberger@punkt.de)
 * @since		2010-03-12
 * @package     TYPO3
 * @subpackage  pt_list
 * @version 	$Id$
 */
class tx_ptlist_dateX_testcase extends tx_phpunit_testcase {

	/**
	 * @test
	 */
	public function test_getDateXByEntity() {
		$dateX = tx_ptlist_dateX::getInstance();
		//overwrite today with fix date
		$dateX->setDateX('2010-01-01');
		tx_pttools_assert::isEqual('2010-01-01', $dateX->getDateX() );
		$dateX->_deleteDateX();
	}

	/**
	 * @test
	 */
	public function test_incrementDateXByEntityDay() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-01-01');
		$dateX->incrementDateXByEntity(1, 'day');
		tx_pttools_assert::isEqual('2010-01-02', $dateX->getDateX() );
		$dateX->_deleteDateX();
	}

	/**
	 * @test
	 */
	public function test_incrementDateXByEntityDayTwoDays() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-01-01');
		$dateX->incrementDateXByEntity(1, 'day');
		$dateX->incrementDateXByEntity(1, 'day');
		tx_pttools_assert::isEqual('2010-01-03', $dateX->getDateX() );
		$dateX->_deleteDateX();
	}

	/**
	 * @test
	 */
	public function test_incrementDateXByEntityDayTwoDaysTwo() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-01-01');
		$dateX->incrementDateXByEntity(2, 'day');
		tx_pttools_assert::isEqual('2010-01-03', $dateX->getDateX() );
		$dateX->_deleteDateX();
	}

	/**
	 * @test
	 */
	public function test_incrementDateXByEntityDayOneDayOneMonth() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-01-01');
		$dateX->incrementDateXByEntity(1, 'day');
		$dateX->incrementDateXByEntity(1, 'month');
		tx_pttools_assert::isEqual('2010-02-02', $dateX->getDateX() );
		$dateX->_deleteDateX();
	}

	/**
	 * @test
	 */
	public function test_decrementDateXByEntityDay() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-01-01');
		$dateX->decrementDateXByEntity(1, 'day');
		tx_pttools_assert::isEqual('2009-12-31', $dateX->getDateX() );
		$dateX->_deleteDateX();
	}

	/**
	 * @test
	 */
	public function test_incrementDateXByEntityDayPlusOneDayMinusOneDay() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-01-01');
		$dateX->incrementDateXByEntity(1, 'day');
		$dateX->decrementDateXByEntity(1, 'day');
		tx_pttools_assert::isEqual('2010-01-01', $dateX->getDateX() );
		$dateX->_deleteDateX();
	}

	/**
	 * @test
	 */
	public function test_incrementDateXByEntityWeek() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-01-01');
		$dateX->incrementDateXByEntity(1, 'week');
		tx_pttools_assert::isEqual('2010-01-08', $dateX->getDateX() );
		$dateX->_deleteDateX();
	}

	/**
	 * @test
	 */
	public function test_decrementDateXByEntityWeek() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-01-01');
		$dateX->decrementDateXByEntity(1, 'week');
		tx_pttools_assert::isEqual('2009-12-25', $dateX->getDateX() );
		$dateX->_deleteDateX();
	}

	/**
	 * @test
	 */
	public function test_incrementDateXByEntityMonth() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-01-01');
		$dateX->incrementDateXByEntity(1, 'month');
		tx_pttools_assert::isEqual('2010-02-01', $dateX->getDateX() );
		$dateX->_deleteDateX();
	}

	/**
	 * @test
	 */
	public function test_decrementDateXByEntityMonth() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-01-01');
		$dateX->decrementDateXByEntity(1, 'month');
		tx_pttools_assert::isEqual('2009-12-01', $dateX->getDateX() );
		$dateX->_deleteDateX();
	}

	/**
	 * @test
	 */
	public function test_incrementDateXByEntityYear() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-01-01');
		$dateX->incrementDateXByEntity(1, 'year');
		tx_pttools_assert::isEqual('2011-01-01', $dateX->getDateX() );
		$dateX->_deleteDateX();
	}

	/**
	 * @test
	 */
	public function test_decrementDateXByEntityYear() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-01-01');
		$dateX->decrementDateXByEntity(1, 'year');
		tx_pttools_assert::isEqual('2009-01-01', $dateX->getDateX() );
		$dateX->_deleteDateX();
	}

	/**
	 * @test
	 */
	public function test_dateXIsDeleted() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-01-01');
		$dateX->_deleteDateX();
		tx_pttools_assert::isEmpty($dateX->getDateX());
	}

	/**
	 * @test
	 */
	public function test_dateXAsTimestamp() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-01-01');
		tx_pttools_assert::isEqual('1262300400', $dateX->getDateXAsTimestamp() );
		$dateX->_deleteDateX();
	}

	/**
	 * @test
	 */
	public function test_getFirstDayOfWeekAsTimestamp() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-05-05');
		tx_pttools_assert::isEqual('', $dateX->getFirstDayOfWeekAsTimestamp());
		$dateX->_deleteDateX();
	}

	/**
	 * @test
	 */
	public function test_getLastDayOfWeekAsTimestamp() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-05-05');
		echo $dateX->getLastDayOfWeekAsTimestamp();
		tx_pttools_assert::isEqual('', $dateX->getLastDayOfWeekAsTimestamp());
		$dateX->_deleteDateX();
	}


	/**
	 * @test
	 */
	public function test_getFirstDayOfMonthAsTimestamp() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-05-05');
		tx_pttools_assert::isEqual('', $dateX->getFirstDayOfMonthAsTimestamp());
		$dateX->_deleteDateX();
	}

	/**
	 * @test
	 */
	public function test_getLastDayOfMonthAsTimestamp() {
		$dateX = tx_ptlist_dateX::getInstance();
		$dateX->setDateX('2010-05-05');
		tx_pttools_assert::isEqual('', $dateX->getLastDayOfMonthAsTimestamp());
		$dateX->_deleteDateX();
	}


	#$this->markTestIncomplete("test not implemented");

}
?>