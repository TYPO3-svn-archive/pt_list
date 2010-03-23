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

require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iSingleton.php'; // interface for Singleton design pattern
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_sessionStorageAdapter.php'; // class for session handling

class tx_ptlist_dateX implements tx_pttools_iSingleton {

	/**
	 *
	 * @var string
	 */
	const CURRENTLISTID = 'dateX';

	/**
	 * format to store date X
	 * @var string
	 */
	const FORMAT = 'Y-m-d';

	/**
	 * dateX instance
	 * @var tx_ptlist_dateX
	 */
	private static $uniqueInstance = NULL;

	/**
	 * Returns a unique instance (Singleton) of the object. Use this method instead of the private/protected class constructor.
	 * @param   void
	 * @return  tx_ptlist_dateX  unique instance of the object (Singleton)
	 */
	public static function getInstance() {
		if (self::$uniqueInstance === NULL) {
			$className = __CLASS__;
			self::$uniqueInstance = new $className;

			if ('' == self::load()) {
				$today = date(self::FORMAT);
				self::store($today);
			}
		}
		return self::$uniqueInstance;
	}

	/**
	 * Private class constructor: use getInstance() to get the unique instance of this Singleton object.
	 * @param   void
	 * @return  void
	 */
	private function __construct() {
	}

	/**
	 * Final method to prevent object cloning (using 'clone'), in order to use only the unique instance of the Singleton object.
	 * @param   void
	 * @return  void
	 */
	public final function __clone() {
		trigger_error('Clone is not allowed for '.get_class($this).' (Singleton)', E_USER_ERROR);
	}


	/**
	 * Returns date X
	 * @return string
	 */
	public static function getDateX() {
		$dateX = self::load();
		return $dateX;
	}

	/**
	 *
	 * @return int
	 */
	public static function getDateXAsTimestamp() {
		$dateX = self::load();
		return strtotime($dateX);
	}

	/**
	 * Returns first day of week, default is monday
	 * @param int $adjustment difference to sunday
	 * @return string
	 */
	public static function getFirstDayOfWeek($adjustment = 1) {
		$dateX = self::load();

		$weekday = date_create($dateX)->format('w');
		if($weekday == 0) {
			$weekday = 7;
		}

		$daydiff = $weekday-$adjustment;

		if ($daydiff > 0) {
			$newDateX = date_create($dateX . ' -'.$daydiff . 'days')->format(self::FORMAT);
		}
		else {
			$newDateX = $dateX;
		}
		return $newDateX;
	}

	/**
	 * returns last day of week, default is sunday
	 * @param int $adjustment difference to sunday
	 * @return string
	 */
	public static function getLastDayOfWeek($adjustment = 1) {
		$dateX = self::load();

		$weekday = date_create($dateX)->format('w');
		if($weekday == 0) {
			$weekday = 7;
		}
		$daydiff = 8-$weekday-$adjustment;

		if ($daydiff > 0) {
			$newDateX = date_create($dateX . ' +'.$daydiff . 'days')->format(self::FORMAT);
		}
		else {
			$newDateX = $dateX;
		}
		return $newDateX;
	}

	/**
	 * get first day of month
	 * @return string
	 */
	public static function getFirstDayOfMonth() {
		return date_create(self::getFirstDayOfMonthUnformated())->format(self::FORMAT);
	}

	/**
	 * get last day of month
	 * @return string
	 */
	public static function getLastDayOfMonth() {
		return date_create(self::getFirstDayOfMonthUnformated().' +1 month -1 day')->format(self::FORMAT);
	}

	/**
	 *
	 * @return string
	 */
	protected static function getFirstDayOfMonthUnformated() {
		$dateX = self::load();
		return date_create($dateX)->format('Y-m-01');
	}

	/**
	 * get first day of year
	 * @return string
	 */
	public static function getFirstDayOfYear() {
		return date_create( self::getYear() . '-01-01')->format(self::FORMAT);
	}

	/**
	 * get last day of year
	 * @return string
	 */
	public static function getLastDayOfYear() {
		return date_create( self::getYear() . '12-31')->format(self::FORMAT);
	}

	/**
	 * get year
	 * @return string
	 */
	protected static function getYear() {
		$dateX = self::load();
		return substr($dateX, 0, 4);
	}

	/* For datePicker */
	/**
	 * store given date
	 * @param string $date, format: YYYY-mm-dd
	 * @return void
	 */
	public static function setDateX($date) {
		self::store($date);
	}

	/* For datePager */
	/**
	 * increment date X by entity
	 * @param int $quantity
	 * @param string $entity
	 * @return void
	 */
	public static function incrementDateXByEntity($quantity = 1, $entity = 'day') {
		$currentDateX = self::load();
		$newDateX = date_create($currentDateX . ' +'.$quantity.' '.$entity)->format(self::FORMAT);
		self::store($newDateX);
	}

	/**
	 * decrement date X by entity
	 * @param int $quantity
	 * @param string $entity
	 * @return void
	 */
	public static function decrementDateXByEntity($quantity = 1, $entity = 'day') {
		$currentDateX = self::load();
		$newDateX = date_create($currentDateX . ' -'.$quantity.' '.$entity)->format(self::FORMAT);
		self::store($newDateX);
	}

	/* For session handling */
	/**
	 * load date X
	 * @return string date
	 */
	protected static function load() {
		$sessionKeyPrefix = $GLOBALS['TSFE']->fe_user->user['uid'] . '_' . self::CURRENTLISTID;
		return tx_pttools_sessionStorageAdapter::getInstance()->read($sessionKeyPrefix . '_dateX');
	}

	/**
	 * store date X
	 * @param string $dateX
	 * @return void
	 */
	protected static function store($dateX) {
		$sessionKeyPrefix = $GLOBALS['TSFE']->fe_user->user['uid'] . '_' . self::CURRENTLISTID;
		tx_pttools_sessionStorageAdapter::getInstance()->store($sessionKeyPrefix . '_dateX', $dateX);
	}

	/* only for unit tests to make a clean test environment again!!! */
	public static function _deleteDateX() {
		self::delete();
	}

	protected static function delete() {
		$sessionKeyPrefix = $GLOBALS['TSFE']->fe_user->user['uid'] . '_' . self::CURRENTLISTID;
		tx_pttools_sessionStorageAdapter::getInstance()->delete($sessionKeyPrefix . '_dateX');
	}
}