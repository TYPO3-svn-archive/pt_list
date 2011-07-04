<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Fabrizio Branca (mail@fabrizio-branca.de)
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
 * Class definition for pt_list controller.
 *
 * @version   $Id$
 * @author      Fabrizio Branca <mail@fabrizio-branca.de>
 * @since       2009-01-21
 */



/**
 * Inclusion of external ressources
 */
$pt_tools_path = t3lib_extMgm::extPath('pt_tools');
require_once $pt_tools_path.'res/objects/class.tx_pttools_exception.php';
require_once $pt_tools_path.'res/staticlib/class.tx_pttools_assert.php';
require_once $pt_tools_path.'res/staticlib/class.tx_pttools_debug.php';
require_once $pt_tools_path.'res/objects/class.tx_pttools_registry.php';
require_once $pt_tools_path.'res/objects/class.tx_pttools_sessionStorageAdapter.php';

$pt_list_path = t3lib_extMgm::extPath('pt_list');
require_once $pt_list_path.'view/list/class.tx_ptlist_view_list_itemList.php';
require_once $pt_list_path.'view/list/class.tx_ptlist_view_list_extjsList_headerdata.php';
require_once $pt_list_path.'view/list/class.tx_ptlist_view_list_pager.php';
require_once $pt_list_path.'view/list/class.tx_ptlist_view_list_filterbox.php';
require_once $pt_list_path.'view/list/class.tx_ptlist_view_list_filterbreadcrumb.php';
require_once $pt_list_path.'view/list/class.tx_ptlist_view_list_bookmarks_displayAvailableBookmarks.php';
require_once $pt_list_path.'view/list/class.tx_ptlist_view_list_bookmarks_form.php';
require_once $pt_list_path.'model/class.tx_ptlist_div.php';
require_once $pt_list_path.'model/class.tx_ptlist_pager.php';
require_once $pt_list_path.'model/class.tx_ptlist_filter.php';
require_once $pt_list_path.'model/class.tx_ptlist_bookmark.php';
require_once $pt_list_path.'model/class.tx_ptlist_bookmarkCollection.php';

require_once t3lib_extMgm::extPath('pt_mvc').'classes/class.tx_ptmvc_controllerFrontend.php';



/**
 * Controller class for the "list" controller
 *
 * Available pluginModes:
 * - list
 * - filterbox
 * - pager
 * - bookmarks
 *
 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
 * @since		2009-01-21
 * @package     TYPO3
 * @subpackage  pt_list
 */
class tx_ptlist_controller_list extends tx_ptmvc_controllerFrontend {

	/**
	 * @var string
	 */
	protected $currentlistId;

	/**
	 * @var tx_ptlist_list
	 */
	protected $currentListObject;

	/**
	 * @var tx_ptlist_pager
	 */
	protected $pager;

	/**
	 * @var string
	 */
	protected $listPrefix;

	/**
	 * @var string
	 */
	protected $controllerPrefixId;

	/**
	 * @var string	in case of pluginMode == filterbox, we need a filterbox id
	 */
	protected $filterboxId; // default (by flexform configuration) is "defaultFilterbox"

	/**
	 * @var array	array('actionName' => <actionName>, 'params' => $parameterArray);
	 */
	protected $forcedNextAction = array();

	/**
	 * @var array	local configuration (set in the constructor)
	 */
	protected $localConfiguration = array();

	/**
	 * @var array	list prefix specific paramters (will be merged to params)
	 */
	protected $listPrefixParams = array();



	/***************************************************************************
	 * Overwriting / Adapting methods from tx_ptmvc_controller(Frontend) class
	 **************************************************************************/

	/**
	 * Class constructor
	 *
	 * @example
	 * $myList = new tx_ptlist_controller_list(
	 * 		array(
	 *			'subControllerPrefixPart' => 'bookmarkList',
	 *			'listId' => 'bookmarkList',
	 *			'listObject' => new tx_ptlist_bookmarklist(), // extends tx_ptlist_list
	 * 		)
	 * );
	 * @param  array  localConfiguration
	 * @return void
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since 2009-01-23
	 */
	public function __construct(array $localConfiguration=array()) {

		// save local configuration to a property
		if (!empty($localConfiguration)) {
			$this->localConfiguration = t3lib_div::array_merge_recursive_overrule($this->localConfiguration, $localConfiguration);
			if (TYPO3_DLOG) t3lib_div::devLog('Merging localConfiguration to property', 'pt_list', 0, $localConfiguration);
		}

		parent::__construct();

	}



	/**
	 * EXPERIMENTAL: Trying to re-init the controller after changes are made to list object
	 *
	 * @author Michael Knoll <knoll@punkt.de>
	 * @since  2009-08-19
	 */
	public function reInit() {
		$this->init();
	}



	/**
	 * Bootstrap method
	 *
	 * @param	void
	 * @return	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-01
	 */
	protected function bootstrap() {
		parent::bootstrap();

		/**
		 * Adapt prefixId. This cannot be done in an overridden getPrefixId() method
		 * as we want to append the current cOject's uid to the prefixId.
		 * The getPrefixId() will be called in the class constructor and at this time
		 * we do not have the current cObject attached to the $this->cObj property
		 */

		tx_pttools_assert::isValidUid($this->cObj->data['uid'], false, array('message' => 'No cObj uid found!'));

		$this->prefixId .= '_' . $this->cObj->data['uid'];

		// Append a "subControllerPrefixPart" from localconfiguration. This should be used if this controller is running as a subcontroller
		if (!empty($this->localConfiguration['subControllerPrefixPart'])) {
			$this->prefixId .= '_' . $this->localConfiguration['subControllerPrefixPart'];
		}
	}



	/**
	 * Gets the configuration and sets the currentListId property from the configuration value
	 *
	 * @param 	void
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-15
	 */
	protected function getConfiguration() {

		// get standard MVC configuration (see tx_ptmvc_controllerFrontend::getConfiguration())
		parent::getConfiguration();

		// merge local configuration (set in the constructor) over existing MVC configuration
		if (is_array($this->localConfiguration) && !empty($this->localConfiguration)) {
			$this->conf = t3lib_div::array_merge_recursive_overrule($this->conf, $this->localConfiguration);
			if (TYPO3_DLOG) t3lib_div::devLog('Merging localConfiguration with existing MVC configuration', 'pt_list', 0, $this->localConfiguration);

			// unset the localConfiguration to avoid that the controller will merge settings again
			$this->localConfiguration = array();
		}

		// set some class properties depending on special configuration settings: set list ID for the current list controller from TS config
		$this->currentlistId = $this->conf['listId'];
		tx_pttools_assert::isNotEmptyString($this->currentlistId, array('message' => '"currentlistId" must have a string value'));

		// set listPrefix
		$this->listPrefix = get_class($this) . '_listId_' . $this->currentlistId;

		// as we need the pluginMode already here we fetch the pluginMode here
		$this->getPluginMode();

		$this->filterboxId = $this->conf['filterboxId'];
		if ($this->pluginMode == 'filterbox') {
			tx_pttools_assert::isNotEmptyString($this->filterboxId, array('message' => 'No "filterboxId" found in configuration.'));
		}

		// merge listId specific configuration ("plugin.tx_<condensedExtKey>.controller.<controllerName>.<listPrefix>.") over existing configuration ("plugin.tx_<condensedExtKey>.controller.list.")
		$listIdSpecificConfiguration = $this->_extConf['controller.'][$this->getControllerName().'.'][$this->listPrefix.'.'];
		if (is_array($listIdSpecificConfiguration) && !empty($listIdSpecificConfiguration)) {
			$this->conf = t3lib_div::array_merge_recursive_overrule($this->conf, $listIdSpecificConfiguration);
		}

	}



	/**
	 * Merge listId specific parameters with controller specific paremeters
	 * tx_ptlist_controller_list_<uid> (controller content element specific)
	 * overwrites tx_ptlist_controller_list_listId_<currentListId> (listId specific)
	 *
	 * @param 	void
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-15
	 */
	protected function getParameters() {

		// get standard parameters form tx_ptmvc_controllerFrontend class
		parent::getParameters();

		tx_pttools_assert::isNotEmptyString($this->listPrefix, array('message' => 'No "listPrefix" found.'));

		// merge parameters
		if (t3lib_div::compat_version('4.3')) {
			$this->listPrefixParams = t3lib_div::_GPmerged($this->listPrefix);
		} else {
			$this->listPrefixParams = t3lib_div::GParrayMerged($this->listPrefix);
		}

		$this->params = t3lib_div::array_merge_recursive_overrule($this->listPrefixParams, $this->params);
	}




	/**
	 * Get view
	 *
	 * @param string $viewName (optional) Name of view
	 * @return tx_ptmvc_view View for filter user interface
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since  2010-05-03
	 */
	public function getView($viewName='') {

		$view = parent::getView($viewName);

		// add appendToUrl variable to view if configured
		if ($this->conf['appendFilterValuesToUrls']) {
			$appendToUrl = $this->getCurrentListObject()->getCompleteListStateAsUrlParameters(false, $this->filterIdentifier);
			$view->addItem($appendToUrl, 'appendToUrl', false);
		}

		return $view;

	}



	/**
	 * Setup / create list object
	 *
	 * @return void
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since 2010-04-30
	 */
	protected function setupListObject() {

		tx_pttools_assert::isNull($this->currentListObject, array('message' => 'The list object has already been created before'));

		/**
		 * Take passed list object (must be an instance from tx_ptlist_list).
		 * e.g. pass a reference to the listObject in the localConfiguration passed to this controller in the constructor
		 *
		 * $listController = new tx_ptlist_controller_list(array(
		 * 		'listObject' => $listObject;
		 * 		[...]
		 * ));
		 */
		if (!empty($this->conf['listObject'])) {
			tx_pttools_assert::isInstanceOf($this->conf['listObject'], 'tx_ptlist_list');
			$listObject = $this->conf['listObject'];
		} else {
			// Create a new instance of the class defined in "listClass" as the listObject
			tx_pttools_assert::isNotEmptyString($this->conf['listClass'], array('message' => 'No "listClass" found in configuration!'));
			$listObject = t3lib_div::getUserObj($this->conf['listClass']);
		}

		/* @var $listObject tx_ptlist_list */

		// store reference to listObject into the registry
		tx_pttools_registry::getInstance()->register($this->currentlistId.'_listObject', $listObject);

			// setup the current listObject (set list ID & prepare columns, filters etc. for the list)
		$listObject->set_listId($this->currentlistId);
		$listObject->setup();

		$this->setCurrentListObject($listObject);
	}



	/**
	 * Get current list object
	 *
	 * @return tx_ptlist_list list object
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since 2010-04-30
	 */
	protected function getCurrentListObject() {
		return $this->currentListObject;
	}



	/**
	 * Set current list object
	 *
	 * @param tx_ptlist_list $listObject
	 * @return void
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since 2010-04-30
	 */
	protected function setCurrentListObject(tx_ptlist_list $listObject) {
		$this->currentListObject = $listObject;
	}



	/**
	 * Init will be executed directly before executing the action method
	 * List, Pager and Filterbox will be updated here
	 *
	 * @param 	void
	 * @return	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-15
	 */
	protected function init() {

		$registry = tx_pttools_registry::getInstance();

		// if no processed state flag for current list found in registry: process list
		if (!isset($registry[$this->currentlistId.'_isProcessed']) || $registry[$this->currentlistId.'_isProcessed'] == false) {

			if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Executing the init method in "%s"', $this->prefixId), 'pt_list');

			// store reference to this controller object into the registry
			if (!isset($registry[$this->currentlistId.'_listControllerObject'])) {
				$registry[$this->currentlistId.'_listControllerObject'] = $this;
			}

			$this->setupListObject();

			$serializedFilterCollection = $this->restoreFilterCollection();

			if (!empty($serializedFilterCollection)) {
				$filterCollection = unserialize($serializedFilterCollection);
				tx_pttools_assert::isInstanceOf($filterCollection, 'tx_ptlist_filterCollection', array('message' => sprintf('Class "%s" does not match "tx_ptlist_filterCollection"', get_class($filterCollection))));
				$this->getCurrentListObject()->invokeFilterCollection($filterCollection);
			}

			$this->processSorting();

			// process filter sub controllers (processes all subcontrollers and retrieves the where clause from them)
			$this->getCurrentListObject()->update();

			$this->initPager();

			$serializedFilterCollection = serialize($this->getCurrentListObject()->getAllFilters());

			// store serialized filters into session
			if (!$this->conf['doNotUseSession']) {
				if (TYPO3_DLOG) t3lib_div::devLog('Storing serialized filterCollection to session', 'pt_list', 0, $serializedFilterCollection);
				tx_pttools_sessionStorageAdapter::getInstance()->store($GLOBALS['TSFE']->fe_user->user['uid'] . '_' . $this->currentlistId . '_filter', $serializedFilterCollection, false);
			}

			if ($this->params['action'] == 'addBookmark') {
				$this->createBookmark($this->params['bookmark_name'], $serializedFilterCollection);
			}

			// store processed state flag for list into the registry
			$registry[$this->currentlistId.'_isProcessed'] = true;
		}

		// set current listObject and pager from appropriate references stored in registry
		$this->setCurrentListObject($registry[$this->currentlistId.'_listObject']);
		$this->pager = $registry[$this->currentlistId.'_pager'];
	}



	/**
	 * Sets the sorting of the list by reading parameters or restoring the state from the session
	 * (Refactoring "extractMethod" from originally init() method)
	 *
	 * @param void
	 * @return void
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since 2009-12-09
	 */
	protected function processSorting() {
		if (!empty($this->params['sorting_column']) && !empty($this->params['sorting_direction'])) {
			// if sorting action submitted: set submitted sorting parameters in current list and store them into session
			$this->getCurrentListObject()->setSortingParameters($this->params['sorting_column'], $this->params['sorting_direction']);

			// store sorting info into session
			if (!$this->conf['doNotUseSession']) {

				$sessionKeyPrefix = $GLOBALS['TSFE']->fe_user->user['uid'] . '_' . $this->currentlistId;

				tx_pttools_sessionStorageAdapter::getInstance()->store($sessionKeyPrefix . '_sortingColumn', $this->params['sorting_column']);
				tx_pttools_sessionStorageAdapter::getInstance()->store($sessionKeyPrefix . '_sortingDirection', $this->params['sorting_direction']);

			}

		} elseif (!$this->conf['doNotUseSession']) {
			// read sorting parameters from session
			$sortingColumn = tx_pttools_sessionStorageAdapter::getInstance()->read($GLOBALS['TSFE']->fe_user->user['uid'] . '_' . $this->currentlistId . '_sortingColumn');
			$sortingDirection = tx_pttools_sessionStorageAdapter::getInstance()->read($GLOBALS['TSFE']->fe_user->user['uid'] . '_' . $this->currentlistId . '_sortingDirection');
			if (!empty($sortingColumn) && !empty($sortingDirection)) {
				$this->getCurrentListObject()->setSortingParameters($sortingColumn, $sortingDirection);
			}
		}
	}



	/**
	 * Init pager
	 * (Refactoring "extractMethod" from originally init() method)
	 *
	 * @param void
	 * @return void
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since 2009-12-09
	 */
	protected function initPager() {
		// create and configure pager object and store reference to it into the registry
		$this->pager = new tx_ptlist_pager();
		if (!empty($this->conf['itemsPerPage'])) {
			$this->pager->set_itemsPerPage($this->conf['itemsPerPage']);
		}
		if (!empty($this->conf['maxRows'])) {
			$this->pager->set_maxRows($this->conf['maxRows']);
		}
		$this->pager->set_itemCollection($this->getCurrentListObject());
		$this->pager->set_currentPageNumber(!empty($this->params['page']) ? $this->params['page'] : 1);
		tx_pttools_registry::getInstance()->register($this->currentlistId.'_pager', $this->pager);
	}


	/**
	 * Stores the current state as bookmark
	 *
	 * @param string $bookmarkName
	 * @param string $serializedFilterCollection
	 * @return void
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since 2009-12-09
	 */
	protected function createBookmark($bookmarkName, $serializedFilterCollection) {
		tx_pttools_assert::isNotEmptyString($bookmarkName, array('message' => 'Empty bookmark name!'));
		$bookmark = new tx_ptlist_bookmark($bookmarkName);
		$bookmark->set_name();
		$bookmark->set_feuser($GLOBALS['TSFE']->fe_user->user['uid']);
		$bookmark->set_list($this->currentlistId);
		$bookmark->set_filterstates($serializedFilterCollection);
		$bookmark->storeSelf();
	}


	/**
	 * Restores the filter collection
	 * - using a bookmark from configuration OR
	 * - using a bookmark from parameter OR
	 * - using the session
	 *
	 * @param void
	 * @return string serialized filter collection
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since 2009-12-09
	 */
	protected function restoreFilterCollection() {
		if (!empty($this->conf['bookmark_uid'])) {

			// load filter states from bookmark
			tx_pttools_assert::isValidUid($this->conf['bookmark_uid'], false, array('message' => 'No valid "bookmark_uid" found!'));
			$bookmark = new tx_ptlist_bookmark($this->conf['bookmark_uid']);
			$serializedFilterCollection = $bookmark->get_filterstate();
			if (TYPO3_DLOG) t3lib_div::devLog('Loaded serialized filterstate from bookmark (by configuration)', 'pt_list', 1, $serializedFilterCollection);

		} elseif (!empty($this->params['bookmark_uid'])) {

			// load filter states from bookmark
			tx_pttools_assert::isValidUid($this->params['bookmark_uid'], false, array('message' => 'No valid "bookmark_uid" found!'));
			$bookmark = new tx_ptlist_bookmark($this->params['bookmark_uid']);
			$serializedFilterCollection = $bookmark->get_filterstate();
			if (TYPO3_DLOG) t3lib_div::devLog('Loaded serialized filterstate from bookmark (by parameter)', 'pt_list', 1, $serializedFilterCollection);

		} elseif (!$this->conf['doNotUseSession']) {
			// restore filter collection from session
			$serializedFilterCollection = tx_pttools_sessionStorageAdapter::getInstance()->read($GLOBALS['TSFE']->fe_user->user['uid'] . '_' . $this->currentlistId . '_filter', false);
			if (TYPO3_DLOG) t3lib_div::devLog('Serialized filterCollection from session', 'pt_list', 0, $serializedFilterCollection);
		}

		return $serializedFilterCollection;
	}



	/**
	 * Overwrite the doAction method to call the "forced next action" if set instead of the original action.
	 * TODO: is this a feature that can/should/may be moved to tx_ptmvc_controllerFrontend?
	 *
	 * @param 	string	(optional) name of the action, if empty the method is trying the default actions
	 * @param 	array	(optional) additional parameters to be passed to the action
	 * @return 	string	HTML Output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-26
	 */
	public function doAction($action = '', array $parameter = array()) {

		if (isset($this->forcedNextAction['actionName'])) {

			$originalAction = $action;

			// copy actionName and parameters to local variables
			$action = $this->forcedNextAction['actionName'];
			$parameter = $this->forcedNextAction['params'];

			// reset class variable
			$this->forcedNextAction = array();

			if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Executing "%s" instead of "%s"', $action, $originalAction), 'pt_list');
		}

		return parent::doAction($action, $parameter);

	}



	/***************************************************************************
	 * Default pluginMode Action Methods
	 **************************************************************************/

	/**
	 * Displays the filterbox
	 *
	 * @param 	void
	 * @return 	string HTML output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-15
	 */
	protected function filterboxDefaultAction() {

		tx_pttools_assert::isNotEmptyString($this->filterboxId, array('message' => 'No "filterboxId" found!'));

		$view = $this->getView('list_filterbox');

		$fiterCollection = $this->getCurrentListObject()->getAllFilters(true, $this->filterboxId, true)->getMarkerArray(); /* @var $filterCollection array */
		$view->addItem($fiterCollection, 'filterbox', false);  // do not filter HTML here since the complete filterbox is already rendered as HTML
		$view->addItem($this->filterboxId, 'filterboxId');

		$resetLinkPid = !empty($this->conf['resetLinkPid']) ? $this->conf['resetLinkPid'] : $GLOBALS['TSFE']->id;
		$view->addItem($resetLinkPid, 'resetLinkPid');

		return $view->render();
	}



	/**
	 * Nav links
	 *
	 * @param string (optional) csl of navlink identifiers. If empty those will be fetched from configuration
	 * @return string HTML output
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since 2010-01-19
	 */
	public function navLinkDefaultAction($navLinks=NULL) {

		// try getting navLinks from parameters if not found in method arguments
		if (empty($navLinks) && !empty($this->params['navLinks'])) {
			$navLinks = $this->params['navLinks'];
		}

		// get navLinks from configuration
		if (empty($navLinks)) {
			$navLinks = $this->conf['navLinks'];
		}
		tx_pttools_assert::isNotEmptyString($navLinks);

		$output = '';
		foreach (t3lib_div::trimExplode(',', $navLinks) as $navLink) {
			$output .= $this->getCurrentListObject()->getNavLink($navLink);
		}

		return $output;
	}



	/**
	 * Displays the pager navigation
	 *
	 * @param 	void
	 * @return 	string HTML output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-15
	 */
	protected function pagerDefaultAction() {

		// setup pagerStrategy

		// check if class file exists
		tx_pttools_assert::isNotEmptyString($this->conf['pagerStrategyClass'], array('message' => 'No "pagerType" found in configuration!'));
		$file = implode(':', array_slice(t3lib_div::trimExplode(':', $this->conf['pagerStrategyClass']), 0, -1));
		tx_pttools_assert::isFilePath($file, array('message' => sprintf('File "%s" not found', $file)));

		$pagerStrategy = t3lib_div::getUserObj($this->conf['pagerStrategyClass']); /* @var $pagerStrategy tx_ptlist_iPagerStrategy */
		if (!empty($this->conf['pagerStrategyConfiguration.'])) {
			$pagerStrategy->setConfiguration($this->conf['pagerStrategyConfiguration.']);
		}

		$this->pager->set_pagerStrategy($pagerStrategy);

		$view = $this->getView('list_pager');
		$view->addItem($this->pager->getMarkerArray(), 'pager');

		$appendToUrl = '';
		if ($this->conf['appendFilterValuesToUrls']) {
			$appendToUrl = $this->getCurrentListObject()->getCompleteListStateAsUrlParameters();
		}
		$view->addItem($appendToUrl, 'appendToUrl', false);


		return $view->render();
	}



	/**
	 * Displays the list itself
	 *
	 * @param 	void
	 * @return 	string HTML output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-15
	 */
	protected function listDefaultAction() {

		// hide columns from configuration
		foreach(t3lib_div::trimExplode(',', $this->getCurrentListObject()->get_hideColumns(), 1) as $columnIdentifier) {
			$this->getCurrentListObject()->getAllColumnDescriptions()->getItemById($columnIdentifier)->set_hidden(true);
		}

		// hide columns from filters
		foreach($this->getCurrentListObject()->getAllFilters() as $filter) { /* @var $filter tx_ptlist_filter */
			if ($filter->get_isActive()) {
				foreach(t3lib_div::trimExplode(',', $filter->get_hideColumns(), 1) as $columnIdentifier) {
					$this->getCurrentListObject()->getAllColumnDescriptions()->getItemById($columnIdentifier)->set_hidden(true);
				}
			}
		}

		// create view
		$view = $this->getView('list_itemList');
		$view->addItem($this->getCurrentListObject()->getListId(), 'listIdentifier');
		$view->addItem($this->getCurrentListObject()->getAllColumnDescriptions(true)->removeHiddenColumns()->getMarkerArray(), 'columns', false); // do not filter HTML here since the column headers could already contain HTML rendered by Typoscript
		$view->addItem($this->getColumnContents(), 'listItems', false);  // do not filter HTML here since the column contents may already be rendered as HTML (e.g. from Typoscript wraps) and the database data is already HTML filtered (see getColumnContents())

		// inline filters
		$view->addItem($this->getCurrentListObject()->getAllFilters(true, 'renderInList', true)->getMarkerArray(), 'filterbox', false);

		// inline pager
		if ($this->conf['inlinePager']) {
			$view->addItem($this->doAction('pagerDefault'), 'pager', false);
			$view->addItem($this->pager->getMarkerArray(), 'pagerData');
		}

		// aggregates
		$view->addItem($this->getAggregateRows(), 'aggregateRows', false);

		// (added by rk 28.08.09) # TODO: Replace this by a translation mechanism
		$view->addItem($this->getCurrentListObject()->get_noElementsFoundText(), 'noElementsFoundText', false); // do not filter HTML here since the display text may already be formatted as HTML (e.g. from Typoscript configuration)

		$appendToSortingUrl = '';
		if ($this->conf['appendFilterValuesToUrls']) {
			$appendToSortingUrl = $this->getCurrentListObject()->getCompleteListStateAsUrlParameters(true);
		}
		$view->addItem($appendToSortingUrl, 'appendToSortingUrl', false);


		// render
		return $view->render();
	}



	/**
	 * Displays the ext js grid, by constructing javascript content
	 * TODO: Javascript shouldn't be constructed dynmically! Better: implement functions that will fetch current column data.
	 *
	 * @param 	void
	 * @return 	string	HTML output (div element that will be replaced by javascript)
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-23
	 */
	protected function extjsListDefaultAction() {

		// id of the div, where ext js will render the grid into
		$element = 'tx-ptlist-grid-'.$this->getCurrentListObject()->get_listId();

		$view = $this->getView('list_extjsList_headerdata');
		$view->addItem($element, 'element');
		$view->addItem($this->conf['itemsPerPage'], 'itemsPerPage');
		$view->addItem($this->getCurrentListObject()->getAllColumnDescriptions()->getMarkerArray(), 'columns', false);
		$view->addItem($this->getCurrentListObject()->getListId(), 'listIdentifier');
		$view->addItem($this->cObj->data['uid'], 'tt_content_uid');
		$view->addItem($this->getCurrentListObject()->getAllFilters(true, 'defaultFilterbox')->getMarkerArray(), 'defaultFilterbox', false);
		$view->addItem($this->getCurrentListObject()->getAllFilters(true, 'topFilterbox')->getMarkerArray(), 'topFilterbox', false);

		// setup and include own headerData
		$GLOBALS['TSFE']->additionalHeaderData['tx_ptlist_grid_'.$this->getCurrentListObject()->get_listId()] = $view->render();

		return '<div id="'.$element.'" class="tx-ptlist-grid"></div>';
	}



	/**
	 * Bookmarks
	 *
	 * @param 	void
	 * @return 	string	HTML output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-10
	 */
	protected function bookmarksDefaultAction() {
		$view = $this->getView('list_bookmarks_displayAvailableBookmarks');

		$bookmarks = new tx_ptlist_bookmarkCollection();
		$bookmarks->loadBookmarksForFeuser($GLOBALS['TSFE']->fe_user->user['uid'], $this->currentlistId);
		$view->addItem($bookmarks, 'bookmarks');
		return $view->render();
	}



	/**
	 * Filter breadcrumb
	 *
	 * @param 	void
	 * @return 	string	HTML output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-10
	 */
	protected function filterbreadcrumbDefaultAction() {
		$view = $this->getView('list_filterbreadcrumb');

		$activeFilterCollection = $this->getCurrentListObject()->getAllFilters(true)->where_isActive();

		$view->addItem($activeFilterCollection->count(), 'activeFilterCount');
		$view->addItem($activeFilterCollection->getMarkerArray(), 'filters', false);
		// filter breadcrumb elements contain html that should not be filtered here. Check is done in the filters.

		return $view->render();
	}



	/**
	 * Bookmark Form
	 *
	 * @param 	void
	 * @return 	string	HTML output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-10
	 */
	protected function bookmarkformDefaultAction() {
		return $this->getView('list_bookmarks_form')->render();
	}



	/***************************************************************************
	 * Additional Action Methods
	 **************************************************************************/

	/**
	 * Display available bookmarks action
	 *
	 * @param 	void
	 * @return 	string 	HTML output (of the new created list controller...)
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-23
	 */
	protected function displayAvailableBookmarksAction() {
		/*
		$view = $this->getView('list_bookmarks_displayAvailableBookmarks');

		$bookmarks = new tx_ptlist_bookmarkCollection();
		$bookmarks->loadBookmarksForFeuser($GLOBALS['TSFE']->fe_user->user['uid'], $this->currentlistId);
		$view->addItem($bookmarks, 'bookmarks');
		return $view->render();
		*/

		// using the pt_list to display the bookmarks :)

		// Now we create a new list controller that will work as a subcontroller here:
		/**
		 * You can/should set following keys to configure the list controller
		 * - "subControllerPrefixPart": To avoid conflicts caused by using the same namespace, declare a subControllerPrefixPart here
		 * - "listObject": Instead of creating a new list object, this one will be used in the subcontroller
		 * or
		 * - "listClass": Define which class should be used to create a new object
		 *
		 * Further options:
		 * - Overwrite all other config options. The local configuration will be merged over the standard configuration.
		 * So you could e.g. set the "listId" here
		 */
		$localConfiguration = array(
			'subControllerPrefixPart' => (!empty($this->localConfiguration['subControllerPrefixPart']) ? $this->localConfiguration['subControllerPrefixPart'].'_' : '') . 'bookmarklist',
			'listClass' => 'EXT:pt_list/model/typo3Tables/class.tx_ptlist_typo3Tables_list.php:tx_ptlist_typo3Tables_list',
			'listId' => 'bookmarks',
			'pluginMode' => 'list',
		);
		$listController = new tx_ptlist_controller_list($localConfiguration);
		// pass the cObj to the subcontroller
		// $listController->cObj = $this->cObj;
		$listController->cObj = clone $this->cObj;
		unset($listController->cObj->data['pi_flexform']);
		return $listController->main();
	}



	/**
	 * Add bookmark action
	 *
	 * @param 	void
	 * @return 	string 	HTML output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-23
	 */
	protected function addBookmarkAction() {
		// TODO: empty because this is processed in the init() method. Find a better solution than writing emtpy action methods!
		return $this->doAction();
	}



	/**
	 * Get raw data
	 *
	 * @return array raw data
	 * @author Fabrizio Branca <fabrizio.branca@aoemedia.de>
	 * @since 2011-07-04 (<- Independency Day!)
	 */
	protected function getRawDataAction(array $params=array()) {

		tx_pttools_assert::isInstanceOf($this->pager, 'tx_ptlist_pager', array('message' => 'No pager object found!'));
		tx_pttools_assert::isInstanceOf($this->getCurrentListObject(), 'tx_ptlist_list', array('message' => 'No list object found!'));

		$offset = $params['offset'] ? $params['offset'] : 0;
		$rowcount = $params['rowcount'] ? $params['rowcount'] : 0;

		$sortingColumn = false;
		$sortingDirection = false;
		if (!empty($sortingDirection) && !empty($sortingColumn)) {
			$sortingDirection = (strtoupper($sortingDirection) == 'DESC') ? tx_ptlist_columnDescription::SORTINGSTATE_DESC : tx_ptlist_columnDescription::SORTINGSTATE_ASC;
			$this->getCurrentListObject()->setSortingParameters($sortingColumn, $sortingDirection);
		}

		// get itemCollection for the requested page from the pager object
		return $itemCollection = $this->pager->getItemCollection($rowcount, $offset);
	}



	/**
	 * Get raw column content
	 *
	 * @return array column content
	 * @author Fabrizio Branca <fabrizio.branca@aoemedia.de>
	 * @since 2011-07-04 (<- Independency Day!)
	 */
	protected function getRawColumnsAction(array $params=array()) {

		tx_pttools_assert::isInstanceOf($this->pager, 'tx_ptlist_pager', array('message' => 'No pager object found!'));
		tx_pttools_assert::isInstanceOf($this->getCurrentListObject(), 'tx_ptlist_list', array('message' => 'No list object found!'));

		$offset = $params['offset'] ? $params['offset'] : 0;
		$rowcount = $params['rowcount'] ? $params['rowcount'] : 0;

		$sortingColumn = $params['sortingColumn'] ? $params['sortingColumn'] : false;
		$sortingDirection = $params['sortingDirection'] ? $params['sortingDirection'] : false;

		if (!empty($sortingDirection) && !empty($sortingColumn)) {
			$sortingDirection = (strtoupper($sortingDirection) == 'DESC') ? tx_ptlist_columnDescription::SORTINGSTATE_DESC : tx_ptlist_columnDescription::SORTINGSTATE_ASC;
			$this->getCurrentListObject()->setSortingParameters($sortingColumn, $sortingDirection);
		}

		// get itemCollection for the requested page from the pager object
		$itemCollection = $this->pager->getItemCollection($rowcount, $offset);

		// render itemCollection into marker array
		$listItems = array();
		foreach ($itemCollection as $itemObj) {
			$listItem = array();

			foreach ($this->getCurrentListObject()->getAllColumnDescriptions(true) as $columnDescription) { /* @var $columnDescription tx_ptlist_columnDescription */

				$dataDescriptionIdentifiers = $columnDescription->get_dataDescriptions()->getDataDescriptionIdentifiers();

				// collect values for each dataDescriptionIdentifier
				$values = array();
				foreach ($dataDescriptionIdentifiers as $dataDescriptionIdentifier) {
					if (!isset($itemObj[$dataDescriptionIdentifier])) {
						throw new tx_pttools_exception(sprintf('Property "%s" not found (via ArrayAccess)', $dataDescriptionIdentifier));
					}
					$values[$dataDescriptionIdentifier] = $itemObj[$dataDescriptionIdentifier];
				}

				$listItem[$columnDescription->get_columnIdentifier()] = $columnDescription->renderFieldContent($values);
			}

			$listItems[] = $listItem;
		}
		return $listItems;
	}



	/**
	 * Returns data in JSON format directly to the client.
	 * This action will be executed asynchronously!
	 *
	 * @param 	void
	 * @return 	void 	(does not return)
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-29
	 */
	protected function fetchDataAction() {
		try {

			$listItems = $this->doAction('getRawColumns', array(
				'offset' => t3lib_div::_GP('start'),
				'rowcount' => t3lib_div::_GP('limit'),
				'sortingColumn' => t3lib_div::_GP('sort'),
				'sortingDirection' => t3lib_div::_GP('dir')
			));

			$count = $this->pager->get_totalItemCount();
			$data = '{"success": true, "count": '.$count.', "listitems":' . json_encode($listItems) . '}';
		} catch (Exception $exception) {
			$data = '{"success": false, "message": '.$exception->__toString().'}';
		}
		// output data directly
		ob_clean();

		header('Cache-Control: no-cache, must-revalidate');
		header('Content-type: application/json');
		header("Content-Length: ".strlen($data));

		echo $data;
		exit();
	}



	/**
	 * Dummy method "changeSortingOrder"
	 * - calls '' (default action)
	 * This action does nothing as it was already processed in the init method.
	 *
	 * @param 	void
	 * @return 	string
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-26
	 */
	protected function changeSortingOrderAction() {
		// this will be processed in the init() method
		return $this->doAction();
	}



	/***************************************************************************
	 * Additional Methods
	 **************************************************************************/

	/**
	 * Return a marker array for all aggregate rows
	 *
	 * @param 	void
	 * @return 	array	markerArray array('<rowKey>' => array('<columnDescriptionIdentifier>' => '<renderedAggregateValue>', [...]), [...]);
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-23
	 */
	protected function getAggregateRows() {

		tx_pttools_assert::isInstanceOf($this->pager, 'tx_ptlist_pager', array('message' => 'No pager object found!'));
		tx_pttools_assert::isInstanceOf($this->getCurrentListObject(), 'tx_ptlist_list', array('message' => 'No list object found!'));

		$markerArray = array();

		if (!(method_exists($this->getCurrentListObject(), 'getAggregateRowInfo') && method_exists($this->getCurrentListObject(), 'getAllAggregates'))) {
			return $markerArray; // return empty marker array
		}

		$aggregateRowsConfig = $this->getCurrentListObject()->getAggregateRowInfo();

		if (is_array($aggregateRowsConfig) && !empty($aggregateRowsConfig)) {

			$aggregateData = $this->pager->getAggregateDataForPage(); // this calls listObject's getAllAggregates() method

			if (TYPO3_DLOG) t3lib_div::devLog('Aggregated data', 'pt_list', 0, $aggregateData);

			tx_pttools_assert::isNotEmptyArray($aggregateData, array('message' => sprintf('No aggregated data found for list "%s"!', $this->getCurrentListObject()->get_listId())));

			$columnDescriptionCollection = $this->getCurrentListObject()->getAllColumnDescriptions(true)->removeHiddenColumns();

			foreach ($aggregateRowsConfig as $rowKey => $row) {
				$markerArray[$rowKey] = array();

				// iterate over all available columns
				foreach ($columnDescriptionCollection->keys() as $columnDescriptionIdentifier) { /* @var $columnDescriptionIdentifier string */

					// check if we have an aggrate entry for this field
					if (is_array($row[$columnDescriptionIdentifier])) {

						// retrieve all aggregates referenced in "aggregateDataDescriptionIdentifier"
						$values = array(); // array(<aggregateDataDescriptionIdentifier> => <rawAggregateDataDescriptionValue>)
						foreach (t3lib_div::trimExplode(',', $row[$columnDescriptionIdentifier]['aggregateDataDescriptionIdentifier'], 1) as $aggregateDataDescriptionIdentifier) { /* @var $aggregateDataDescriptionIdentifier string */
							if (!isset($aggregateData[$aggregateDataDescriptionIdentifier])) {
								throw new tx_pttools_exception(sprintf('Could not find aggregate value for "%s"', $aggregateDataDescriptionIdentifier));
							}
							$values[$aggregateDataDescriptionIdentifier] = $aggregateData[$aggregateDataDescriptionIdentifier];
						}

						// render field content
						$markerArray[$rowKey][$columnDescriptionIdentifier] = tx_ptlist_div::renderValues($values, $aggregateRowsConfig[$rowKey][$columnDescriptionIdentifier]);

					} else {

						// there is now aggregate defined for this field in this row
						$markerArray[$rowKey][$columnDescriptionIdentifier] = '';

					}
				}
			}
		}
		return $markerArray;
	}



	/**
	 * Gets the column contents of the current page from the list object asking the pager
	 *
	 * @param	void
	 * @return	array	array of array(<columnIdentifier> => <renderedColumnContent>)
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-20
	 */
	protected function getColumnContents() {

		tx_pttools_assert::isInstanceOf($this->pager, 'tx_ptlist_pager', array('message' => 'No pager object found!'));
		tx_pttools_assert::isInstanceOf($this->getCurrentListObject(), 'tx_ptlist_list', array('message' => 'No list object found!'));


		/**
		 * Get itemCollection for the requested page from the pager object
		 *
		 * The itemCollection is a traversable object that contains arrays or objects implementing the ArrayAccess interface.
		 * These objects/arrays contain a value for each dataDescriptionIdentifer.
		 * That means the itemCollection contains the "raw" data from the database before it is being composed and rendered into columns
		 */
		$itemCollection = $this->pager->getItemCollectionForPage();

		// __Check requirement 1__: itemCollection must be traversable!
		tx_pttools_assert::isInstanceOf($itemCollection, 'Traversable', array('message' => 'Return object is not traversable!'));

		$columnDescriptionCollection = $this->getCurrentListObject()->getAllColumnDescriptions(true)->removeHiddenColumns();

		// render itemCollection into marker array
		$listItems = array();
		foreach ($itemCollection as $itemObj) {

			// __Check requirement 2__: itemCollection's elements must be an array or implement the ArrayAccess interface!
			if (!(is_array($itemObj) || $itemObj instanceof ArrayAccess)) {
				throw new tx_pttools_exception('ItemObj must be an array or implement the ArrayAccess interface!');
			}

			$listItem = array(); // array(<columnIdentifier> => <renderedColumnContent>)

			foreach ($columnDescriptionCollection as $columnDescription) { /* @var $columnDescription tx_ptlist_columnDescription */

				$dataDescriptionIdentifiers = $columnDescription->get_dataDescriptions()->getDataDescriptionIdentifiers();

				// collect values for each dataDescriptionIdentifier
				$values = array(); // array(<dataDescriptionIdentifier> => <rawDataDescriptionValue>)
				foreach ($dataDescriptionIdentifiers as $dataDescriptionIdentifier) { /* @var $dataDescriptionIdentifier string */
					if (!isset($itemObj[$dataDescriptionIdentifier])) {
						throw new tx_pttools_exception(sprintf('Property "%s" not found (via ArrayAccess)', $dataDescriptionIdentifier));
					}

					/**
					 * XSS prevention: Use plugin.tx_ptlist.view.csv_rendering.filterHtml = 1 in your TS Setup to
					 * filter all values
					 */
					$tsHtmlFiltering = tx_pttools_div::getTS('plugin.tx_ptlist.view.filterHtml');
					$filterHtml = true;     // filter HTML by default
					if ( $tsHtmlFiltering === 0 || $tsHtmlFiltering === '0' ) {
						$filterHtml = false;
					}

					if ($filterHtml) {
						// Here only raw data is filtered, BEFORE data is rendered by TS setup
						$values[$dataDescriptionIdentifier] = tx_pttools_div::htmlOutput($itemObj[$dataDescriptionIdentifier]); // added HTML filtering by default (rk 09.05.2009) - TODO: check implementation at this place and make HTML filtering configurable for each dataDescriptionIdentifier via Typoscript
					} else {
						// Filtering is deactivated by TS
						$values[$dataDescriptionIdentifier] = $itemObj[$dataDescriptionIdentifier];
					}

				}
				$listItem[$columnDescription->get_columnIdentifier()] = $columnDescription->renderFieldContent($values);
			}
			$listItems[] = $listItem;
		}

		return $listItems;
	}




	/***************************************************************************
	 * Getter/Setter
	 **************************************************************************/

	/**
	 * Returns the list prefix
	 *
	 * @param   void
	 * @return  string  list prefix
	 * @author  Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since   2009-01-23
	 */
	public function get_listPrefix() {
		return $this->listPrefix;
	}



	/**
	 * Returns the current list id
	 *
	 * @param void
	 * @return string list id
	 * @author Michael Knoll <knoll@punkt.de>
	 * @since 2009-06-17
	 */
	public function get_currentlistId() {
		return $this->currentlistId;
	}



	/**
	 * Set forced next action.
	 *
	 * @param   string  action name
	 * @param   array   (optional) array of parameters passed to the action
	 * @author  Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since   2009-01-26
	 */
	public function set_forcedNextAction($actionName, array $params=array()) {
		if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Setting "forcedNextAction" to "%s"', $actionName), 'pt_list', 0, $params);

		// tx_pttools_assert::isNotEmptyString($actionName, array('message' => 'No valid "actionName"!'));
		$this->forcedNextAction = array(
			'actionName' => $actionName,
			'params' => $params,
		);
	}


}

?>
