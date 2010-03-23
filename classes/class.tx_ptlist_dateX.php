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

class tx_ptlist_dateX implements tx_pttools_iSingleton {

	const CURRENTLISTID = 'dateX';
	const FORMAT = 'Y-m-d';

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


	public function getDateX() {
		$dateX = self::load();
		return $dateX;
	}

	public function getDateXAsTimestamp() {
		$dateX = self::load();
		return strtotime($dateX);
	}

	public function getFirstDayOfWeek($adjustment = 1) {
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

	public function getLastDayOfWeek($adjustment = 1) {
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

	public function getFirstDayOfMonth() {
		$newDateX = date_create(self::getFirstDayOfMonthUnformated())->format(self::FORMAT);
		return $newDateX;
	}

	public function getLastDayOfMonth() {
		$newDateX = date_create(self::getFirstDayOfMonthUnformated().' +1 month -1 day')->format(self::FORMAT);
		return $newDateX;
	}

	protected function getFirstDayOfMonthUnformated() {
		$dateX = self::load();
		$newDateX = date_create($dateX)->format('Y-m-01');
		return $newDateX;
	}

	public function getFirstDayOfYear() {
		$dateX = self::load();
		$newDateX = date_create( self::getYear() . '-01-01')->format(self::FORMAT);
		return $newDateX;
	}

	public function getLastDayOfYear() {
		$dateX = self::load();
		$newDateX = date_create( self::getYear() . '12-31')->format(self::FORMAT);
		return $newDateX;
	}

	protected function getYear() {
		$dateX = self::load();
		return substr($dateX, 0, 4);
	}

	/* For datePicker */
	public function setDateX($date) {
		self::store($date);
	}

	/* For datePager */
	public function incrementDateXByEntity($quantity = 1, $entity = 'day') {
		$currentDateX = self::load();
		$newDateX = date_create($currentDateX . ' +'.$quantity.' '.$entity)->format(self::FORMAT);
		self::store($newDateX);
	}

	public function decrementDateXByEntity($quantity = 1, $entity = 'day') {
		$currentDateX = self::load();
		$newDateX = date_create($currentDateX . ' -'.$quantity.' '.$entity)->format(self::FORMAT);
		self::store($newDateX);
	}

	/* For session handling */
	protected function load() {
		$sessionKeyPrefix = $GLOBALS['TSFE']->fe_user->user['uid'] . '_' . self::CURRENTLISTID;
		return tx_pttools_sessionStorageAdapter::getInstance()->read($sessionKeyPrefix . '_dateX');
	}

	protected function store($dateX) {
		$sessionKeyPrefix = $GLOBALS['TSFE']->fe_user->user['uid'] . '_' . self::CURRENTLISTID;
		tx_pttools_sessionStorageAdapter::getInstance()->store($sessionKeyPrefix . '_dateX', $dateX);
	}

	/* only for unit tests to make a clean test environment again!!! */
	public function _deleteDateX() {
		self::delete();
	}

	protected function delete() {
		$sessionKeyPrefix = $GLOBALS['TSFE']->fe_user->user['uid'] . '_' . self::CURRENTLISTID;
		tx_pttools_sessionStorageAdapter::getInstance()->delete($sessionKeyPrefix . '_dateX');
	}
}