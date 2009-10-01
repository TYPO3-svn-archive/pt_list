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
 * Class file definition for pt_list filter class
 *
 * $Id$
 *
 * @author  Fabrizio Branca <mail@fabrizio-branca.de>
 * @since   2009-01-20
 */



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_exception.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_assert.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_debug.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_div.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iTemplateable.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/abstract/class.tx_pttools_iSettableByArray.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_registry.php';


require_once t3lib_extMgm::extPath('pt_mvc').'classes/class.tx_ptmvc_controllerFrontend.php';

require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_dataDescriptionCollection.php';
require_once t3lib_extMgm::extPath('pt_list').'view/filter/class.tx_ptlist_view_filter_breadcrumb.php';



/**
 * Filter class
 *
 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
 * @since	2009-01-20
 * @package TYPO3
 * @subpackage pt_list
 */
abstract class tx_ptlist_filter extends tx_ptmvc_controllerFrontend implements tx_pttools_iTemplateable, Serializable, tx_pttools_iSettableByArray {

	/**
	 * @var string
	 */
	protected $listIdentifier;

	/**
	 * @var string
	 */
	protected $filterIdentifier;

	/**
	 * @var string
	 */
	protected $filterboxIdentifier = 'defaultFilterbox';

	/**
	 * @var tx_ptlist_dataDescriptionCollection
	 */
	protected $dataDescriptions;

	/**
	 * @var bool
	 */
	protected $hasUserInterface = true;

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * @var string
	 */
	protected $submitLabel;

	/**
	 * @var bool
	 */
	protected $isActive = false;

	/**
	 * @var mixed	current filter value
	 */
	protected $value;

	/**
	 * @var string
	 */
	protected $dependsOn;

	/**
	 * @var string	csl of columnDescriptionIdentifiers
	 */
	protected $hideColumns;

	/**
	 * @var bool	if true, the sql statement will be inverted
	 */
	protected $invert = false;



	/**
	 * Overwriting default property value from tx_ptmvc_controllerFrontend:
	 * As filter controllers are not called directly from TYPO3 as frontend plugins (they are called by tx_ptlist_controller_list)
	 * they do not have any flexform configuration and the do not have any cObj attached (which would cause an error when trying to
	 * retrieve the flexform configuration)
	 *
	 * @var bool
	 */
	protected $mergeConfAndFlexform = false;


	/***************************************************************************
	 * Overwriting methods from the tx_ptmvc_controller class
	 **************************************************************************/

	/**
	 * Class constructor
	 *
	 * @param 	string	list identifier
	 * @param 	string	filter identifier
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-20
	 */
	public function __construct($listIdentifier='', $filterIdentifier='') {
		$this->listIdentifier = $listIdentifier;
		$this->filterIdentifier = $filterIdentifier;
        $this->dataDescriptions = new tx_ptlist_dataDescriptionCollection();
		parent::__construct();
	}

	/**
	 * Returns the action.
	 * If the dropActionParameter configuration is set this controller action "submit" will be executed
	 * without being given as parameter on the presence of a value
	 *
	 * @param	void
	 * @return 	string	action
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-10-01
	 */
	protected function getAction() {
		$action = parent::getAction();
		if (empty($action)) {
			if ($this->conf['dropActionParameter'] == 1) {
				if (!empty($this->params['value'])) {
					$action = 'submit';
				}
			}
		}
		return $action;
	}



	/**
	 * Overwriting the getPrefixId() method to generate a custom prefixId depending on the list identifier and the filter identifier
	 *
	 * @param 	void
	 * @return 	string 	prefixId
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-20
	 */
	protected function getPrefixId() {

		// tx_pttools_assert::isNotEmptyString($this->filterIdentifier, array('message' => 'No "filterIdentifier" found!'));
		// tx_pttools_assert::isNotEmptyString($this->listIdentifier, array('message' => 'No "listIdentifier" found!'));
		$prefixId = parent::getPrefixId();
		$prefixId .= '_' . $this->listIdentifier . '_' . $this->filterIdentifier;
		return $prefixId;
	}



	/**
	 * Get configuration
	 *
	 * @param 	void
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-26
	 */
	protected function getConfiguration() {

		/*

		// get standard configuration first
		parent::getConfiguration();

		if (is_array($this->conf[$this->filterIdentifier.'.'])) {
			$this->conf = t3lib_div::array_merge_recursive_overrule($this->conf, $this->conf[$this->filterIdentifier.'.']);
			unset($this->conf[$this->filterIdentifier.'.']);
		}

		*/

		// Configuration will be set via setPropertiesFromArray()


		/**
		 * TODO: this mechanism could be used generally to overwrite controller specific configuration with ttcontent specific configuration
		 * e.g. plugin.tx_ptlist_controller.list {
		 * }
		 *  could be overwritten by
		 *
		 * plugin.tx_ptlist_controller.list.tt_content_122 {
		 * }
		 *
		 * Problem: stdWarp and complex subarrays should be resolved before being merged
		 */
	}



	/**
	 * Configuration of filter will be passed to template as 'filter'
	 *
	 * @param  string          $viewName   Name of view
	 * @return tx_ptmvc_view               View for filter user interface
	 * @author Michael Knoll <knoll@punkt.de>
	 * @since  2009-07-17
	 */
	public function getView($viewName='') {

		$view = parent::getView($viewName);
		$view->addItem($this->conf, 'filter');
		return $view;

	}


	/***************************************************************************
	 * Abstract methods for this abstract class
	 **************************************************************************/

	/**
	 * Get the where clause snippet for this filter
     * +++++ IMPORTANT: avoid SQL injections in your implementation!!! +++++
	 *
	 * @param	void
	 * @return 	string	where clause snippet (without "AND")
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	abstract function getSqlWhereClauseSnippet();


    /***************************************************************************
     * Action methods
     **************************************************************************/

    /**
     * Displays the user interface
     * - calls isActiveAction
     * or
     * - calls isNotActiveAction
     * Overwrite this method if you don't want the isActive/isNotActive behaviour
     *
     * @param   void
     * @return  string  HTML output
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-01-20
     */
    public function defaultAction() {
        if ($this->isActive) {
            return $this->doAction('isActive');
        } else {
            return $this->doAction('isNotActive');
        }
    }



    /**
     * Override this method, set your value property there and then call this method afterwards
     *
     * @return  string HTML output
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-02-06
     */
    public function submitAction() {
        $output = '';

        // do validation
        if ($this->validate()) {
            $output = $this->doAction('onValidated');
        } else {
            $output = $this->doAction('onNotValidated');
        }
        return $output;
    }



    /**
     * Reset action
     * - calls '' (default action depending on pluginMode)
     *
     * @param   void
     * @return  string  HTML output
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-01-20
     */
    public function resetAction() {
        $this->reset();
        return $this->doAction();
    }



    /**
     * This method will be called if the user input was validated succesfully by the default "submitAction".
     * If you do not want to return the default method pass an array with the key "doNotReturnDefaultAction" set to true
     *
     * @param   array   parameter array
     * @return  string  HTML output
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>, Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-02-06
     */
    public function onValidatedAction(array $params=array()) {

        if (TYPO3_DLOG) t3lib_div::devLog('onValidatedAction', 'pt_list', 2, $this->conf);
        $this->isActive = true;

        // reset sorting state of filtered list, if set in TS
        if ($this->conf['resetListSortingStateOnSubmit'] == 1) {
            $this->resetListSortingState();
        }

        // reset other filters if set in filter config
        if ($this->conf['resetFilters']) {
            $listObject = tx_pttools_registry::getInstance()->get($this->listIdentifier.'_listObject'); /* @var $listObject tx_ptlist_list */

            // set resetFilters to "__ALL__" to reset all other filters
            if ($this->conf['resetFilters'] == '__ALL__') {
                $exceptFilterId = $this->filterIdentifier;
                $listObject->getAllFilters()->reset($exceptFilterId);
            // reset filters from given filter identifier or comma seprated list of filter identifier
            } else {
                $resetFiltersArray = tx_pttools_div::returnArrayFromCsl($this->conf['resetFilters']);
                if (is_array($resetFiltersArray)) {
                    foreach ($resetFiltersArray as $filterIdentifier) {
                        $listObject->getAllFilters()->getItemById($filterIdentifier)->reset();
                    }
                }
            }
        }

        // execute user functions
        if (is_array($this->conf['onValidated.'])) {
            if (TYPO3_DLOG) t3lib_div::devLog('onValidated userfunctions', 'pt_list', 2, $this->conf['onValidated.']);

            $sortedKeys = t3lib_TStemplate::sortedKeyList($this->conf['onValidated.'], false);

            foreach ($sortedKeys as $key) {

                $funcName = $this->conf['onValidated.'][$key];
                tx_pttools_assert::isNotEmptyString($funcName, array('message' => 'No valid "funcName" found!'));

                $userFuncParams = array(
                    'conf' => $this->conf['onValidated.'][$key.'.']
                );
                t3lib_div::callUserFunction($funcName, $userFuncParams, $this);
                // function return will be ignored
            }
        }

        return ($params['doNotReturnDefaultAction'] == true) ? '' : $this->doAction('');
    }



    /**
     * This method will be called if the user input was not validated succesfully by the default "submitAction"
     * If you do not want to return the default method pass an array with the key "doNotReturnDefaultAction" set to true
     *
     * @param   array   parameter array
     * @return  string  HTML output
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-02-06
     */
    public function onNotValidatedAction(array $params=array()) {
        $this->isActive = false;
        // TODO: remove string in the output
        return ($params['doNotReturnDefaultAction'] == true) ? '' : 'Not validated<br />' . $this->doAction('');
    }



    /**
     * This method will be called to generate the output for the filter breadcrumb.
     * If you want additional functionality or a different output overwrite this method.
     *
     * @param   void
     * @return  string HTML ouput
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-02-06
     */
    public function breadcrumbAction() {
        $view = $this->getView('filter_breadcrumb');
        $view->addItem($this->label, 'label');
        $view->addItem($this->value, 'value');
        return $view->render();
    }



    /**
     * Reset to Typoscript defaults action: resets the filter to the presets set in the filter's Typoscript configuration
     * - calls '' (default action depending on pluginMode)
     *
     * @param   void
     * @return  string  HTML output
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-08-25
     */
    public function resetToTsPresetStateAction() {

    	$this->reset();
    	$this->setPresetStateFromTs();
    	return $this->doAction();

    }



	/***************************************************************************
	 * Methods implementing the domain logic
	 **************************************************************************/

	/**
	 * Invoke an external filter object to this one, so that value and state can be written to itself
	 *
	 * @param 	tx_ptlist_filter 	external filter object (e.g. coming from the session storage)
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-20
	 */
	public function invokeFilter(tx_ptlist_filter $filter) {
		// TODO: is this generic enough to fulfill all needs?
		$this->isActive = $filter->isActive;
		$this->value = $filter->value;
		if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Invoking filter "%s" from session', $filter->get_filterIdentifier()), 'pt_list', 1, array('value' => $filter->value, 'isActive' => $filter->isActive));
	}



	/**
	 * Check if an user has access to this filter by checking if the user has access to all data descriptions used by this filter
	 *
	 * @param 	string	comma-separated list of group uids the user is in
	 * @return 	bool	true, if the user has access to this filter
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-20
	 */
	public function hasAccess($groupList) {
		foreach ($this->dataDescriptions as $dataDescription) { /* @var $dataDescription tx_ptlist_dataDescription */
			if (!$dataDescription->hasAccess($groupList)) {
				return false;
			}
		}
		return true;
	}



	/**
	 * Resets this filter.
	 * Overwrite this method for indidual reset actions
	 *
	 * @param	void
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	public function reset() {
		$this->set_isActive(false);
		$this->value = NULL;

		// reset all filters that depend on this one too
		$filterCollection = tx_pttools_registry::getInstance()->get($this->listIdentifier.'_listObject')->getAllFilters();
		foreach($filterCollection as $filter) { /* @var $filter tx_ptlist_filter */
			if ($filter->get_dependsOn() == $this->filterIdentifier) {
				$filter->reset();
			}
		}
	}



	/**
	 * Resets the filter to the presets set in the filter's Typoscript configuration
	 *
	 * @return void
	 * @author Michael Knoll <knoll@punkt.de>
	 * @since  2009-08-25
	 */
	protected function setPresetStateFromTs() {
	    // setting default filter state
	    $this->setPresetStateFromArray($this->conf);
	}



	/**
	 * Helper method for setting isActive state and default value from array
	 *
	 * @param  array   $dataArray      Array of configuration data
	 * @return void
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-08-25
     */
	protected function setPresetStateFromArray($dataArray) {
	    if (isset($dataArray['isActive'])) {
            $this->isActive = (bool) $dataArray['isActive'];
        }
        if (isset($dataArray['value'])) {
            if (TYPO3_DLOG) t3lib_div::devLog('Setting default value from configuration', 'pt_list', 1, $dataArray['value']);
            if ($dataArray['value.']['isSerialized']) {
                $dataArray['value'] = unserialize($dataArray['value']);
                unset($dataArray['value.']['isSerialized']);
            }
            $dataArray['value'] = $this->cObj->stdWrap($dataArray['value'], $dataArray['value.']);
            $this->value = $dataArray['value'];
        } elseif (is_array($dataArray['value.'])) {
            if (TYPO3_DLOG) t3lib_div::devLog('Setting default value from configuration (array)', 'pt_list', 1, $dataArray['value.']);
            $this->value = tx_pttools_div::stdWrapArray($dataArray['value.']);
        }
	}



	/**
	 * This method will be called to determine if the user input validates.
	 * Overwrite this method in your inheriting class if you use the default "submitAction".
	 *
	 * @param 	void
	 * @return 	bool	true if the user input validates, false otherwise
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-06
	 */
	public function validate() {
		throw new tx_pttools_exception('No "validate" method implemented!');
	}



	/**
	 * Resets sorting states of corresponding list
	 *
	 * @return void
	 * @author Michael Knoll
	 * @since 2009-06-15
	 */
	protected function resetListSortingState() {
		$listObject = tx_pttools_registry::getInstance()->get($this->listIdentifier.'_listObject'); /* @var $listObject tx_ptlist_list */
		$listObject->resetSortingParameters();
	}



	/**
	 * Get the filter value(s) as parameter string intended to be appended to an url to pass
	 * filter states in cases where no sessions should be used.
	 * Override this method in your inheriting filter if you store your values in a different way
	 *
	 * @param void
	 * @return string
	 * @author Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since 2009-10-01
	 */
	public function getFilterValueAsGetParameterString() {
		$parameterString = '';
		if (!empty($this->value)) {
			if (is_scalar($this->value)) {
				$parameterString .= '&'.$this->prefixId.'[value]='.$this->value;
			} elseif (is_array($this->value)) {
				if (count($this->value) > 1) {
					foreach ($this->value as $value) {
						$parameterString .= '&'.$this->prefixId.'[value][]='.$value;
					}
				} else {
					$parameterString .= '&'.$this->prefixId.'[value]='.reset($this->value);
				}
			}
		}
		return $parameterString;
	}



    /***************************************************************************
     * Methods implementing "tx_pttools_iSettableByArray" interface
     **************************************************************************/

	/**
	 * Set properties from array
	 *
	 * @param 	array 	dataArray
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-21
	 */
	public function setPropertiesFromArray(array $dataArray) {
		if (TYPO3_DLOG) t3lib_div::devLog('Setting properties from array in ' . __CLASS__, 'pt_list', 0, $dataArray);

		// set configuration from outside instead of getting it the usual way via getConfiguration()
		$this->conf = $dataArray;

		if (isset($dataArray['listIdentifier'])) {
			$this->listIdentifier = $dataArray['listIdentifier'];
		}
		if (isset($dataArray['filterIdentifier'])) {
			$this->filterIdentifier = $dataArray['filterIdentifier'];
		}
		if (isset($dataArray['filterboxIdentifier'])) {
			$this->filterboxIdentifier = $dataArray['filterboxIdentifier'];
		}
		if (isset($dataArray['hasUserInterface'])) {
			$this->hasUserInterface = (bool) $dataArray['hasUserInterface'];
		}
		if (isset($dataArray['label'])) {
			$this->label = $dataArray['label'];
		}
		if (isset($dataArray['submitLabel'])) {
			$this->submitLabel = $dataArray['submitLabel'];
		}
		if (isset($dataArray['dependsOn'])) {
			$this->dependsOn = $dataArray['dependsOn'];
		}
		if (isset($dataArray['invert'])) {
			$this->invert = (bool)$dataArray['invert'];
		}
		if (isset($dataArray['hideColumns'])) {
			$this->hideColumns = $dataArray['hideColumns'];
		}
		if (isset($dataArray['dataDescriptionIdentifier'])) {
			$dataDescriptionIdentifiers = array();
			$registry = tx_pttools_registry::getInstance();
			if ($dataArray['dataDescriptionIdentifier'] == '*') {
				foreach($registry[$this->listIdentifier.'_listObject']->getAllDataDescriptions()->getAccessibleDataDescriptions($GLOBALS['TSFE']->gr_list) as $dataDescription) { /* @var $dataDescription tx_ptlist_dataDescription */
					$dataDescriptionIdentifiers[] = $dataDescription->get_identifier();
				}
			} else {
				$dataDescriptionIdentifiers = t3lib_div::trimExplode(',', $dataArray['dataDescriptionIdentifier'], true);
			}
			foreach ($dataDescriptionIdentifiers as $dataDescriptionIdentifier) {
				tx_pttools_assert::isNotEmptyString($dataDescriptionIdentifier, array('message' => 'Empty "dataDescriptionIdentifier"!'));
				$this->dataDescriptions->addItem($registry[$this->listIdentifier.'_listObject']->getAllDataDescriptions()->getItemById($dataDescriptionIdentifier));
			}
		}
		/*
		if (!empty($dataArray['dataDescriptionIdentifier'])) {
			$dataDescriptionIdentifiers = t3lib_div::trimExplode(',', $dataArray['dataDescriptionIdentifier']);
			$this->dataDescriptions = new tx_ptlist_dataDescriptionCollection();
			$registry = tx_pttools_registry::getInstance();
			foreach ($dataDescriptionIdentifiers as $dataDescriptionIdentifier) {
				$this->dataDescriptions->addItem($registry[$this->listIdentifier.'_listObject']->getAllDataDescriptions()->getItemById($dataDescriptionIdentifier));
			}
		}
		*/

		// setting default filter state
		// Use proxy method to set isActive and defaultValue, as used in other places also!
		$this->setPresetStateFromArray($this->conf);

		// update prefixId as the listIdentifier and the filterIdentifier influence the prefixId
		$this->prefixId = $this->getPrefixId();
	}



	/***************************************************************************
	 * Methods implementing the "tx_pttools_iTemplateable" interface
	 **************************************************************************/

	/**
	 * Returns a marker array
	 *
	 * @param 	void
	 * @return 	array
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-15
	 */
	public function getMarkerArray() {
		$markerArray = array(
			'name' => get_class($this),
			'label' => $this->label,
		    'submitLabel' => $this->submitLabel,
			'isActive' => $this->get_isActive(),
			'filterPrefixId' => $this->prefixId,
			'filterId' => $this->filterIdentifier,
			'filterClass' => str_replace('_', '-', get_class($this)),
			'hideResetLink' => ($this->conf['hideResetLink'] == true),
		);

		// filter html in the markerArray
		$markerArray = tx_pttools_div::htmlOutputArray($markerArray);

		// "userInterface" and "breadcrumb" may contain html and will not be filtered here!
		$markerArray['userInterface'] = $this->lastRenderedContent;
		$markerArray['breadcrumb'] = $this->doAction('breadcrumb');

		$markerArray['dataDescriptions'] = array();
		foreach ($this->dataDescriptions as $dataDescriptions) { /* @var $dataDescriptions tx_ptlist_dataDescription */
			$markerArray['dataDescriptions'][] = $dataDescriptions->get_identifier();
		}

		return $markerArray;
	}



	/***************************************************************************
	 * Methods implementing the "Serializable" interface
	 **************************************************************************/

	/**
	 * Serialize method
	 * This method will automatically executed when calling
	 * $stringRepresentingObjectImplementingThisClass = serialize($objectImplementingThisClass);
	 *
	 * @param 	void
	 * @return 	string	"safe" string representation of this object
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-20
	 */
	public function serialize() {
		tx_pttools_assert::isNotEmptyString($this->listIdentifier, array('message' => 'Empty list identifier'));
		$state = array(
			'value' => $this->value,
			'isActive' => $this->isActive,
			'listIdentifier' => $this->listIdentifier,
			'filterIdentifier' => $this->filterIdentifier,
			'dependsOn' => $this->dependsOn,
		);
		$state['dataDescriptions'] = array();
		if ($this->dataDescriptions instanceof tx_ptlist_dataDescriptionCollection) {
    		foreach($this->dataDescriptions as $dataDescription) { /* @var $column tx_ptlist_dataDescription */
    			$state['dataDescriptions'][] = $dataDescription->get_identifier();
    		}
	    }

		// if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Serializing "%s" filter "%s", "%s"', get_class($this), $this->filterIdentifier, $this->listIdentifier), 'pt_list', 1, $state);
		return serialize($state);
	}


	/**
	 * Unserialize method
	 * This method will automatically executed when calling
	 * $objectImplementingThisClass = unserialize($stringRepresentingObjectImplementingThisClass);
	 *
	 * TODO ry21: Why is filter not unserialized via $this->setPropertiesFromArray($state)???
	 *
	 * @param 	string	"safe" string representation of this object (generated by the serialize() method)
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-20
	 */
	public function unserialize($serialized) {
		tx_pttools_assert::isNotEmptyString($serialized);

		$state = unserialize($serialized);
		// if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Unserializing "%s" filter', get_class($this)), 'pt_list', 1, $state);

		$this->value = $state['value'];
		$this->isActive = $state['isActive'];
		$this->filterIdentifier = $state['filterIdentifier'];
		$this->listIdentifier = $state['listIdentifier'];
		$this->dependsOn = $state['dependsOn'];
		tx_pttools_assert::isNotEmptyString($this->listIdentifier, array('message' => 'Empty list identifier!'));

		// retrieve references to columnDescription objects from the listObject found in the registry
		$registry = tx_pttools_registry::getInstance();
		$this->dataDescriptions = new tx_ptlist_dataDescriptionCollection();
		if (is_array($state['dataDescriptions'])) {
			foreach ($state['dataDescriptions'] as $dataDescriptionIdentifier) {
	            $this->dataDescriptions->addItem($registry[$this->listIdentifier.'_listObject']->getAllDataDescriptions()->getItemById($dataDescriptionIdentifier));
	        }
		}
	}



    /***************************************************************************
     * Getter / Setter Methods
     **************************************************************************/


    public function get_filterIdentifier() {
        return $this->filterIdentifier;
    }

    public function get_listIdentifier() {
        return $this->listIdentifier;
    }

    public function get_filterboxIdentifier() {
    	return $this->filterboxIdentifier;
    }

    public function set_filterboxIdentifier($filterboxIdentifier) {
    	$this->filterboxIdentifier = $filterboxIdentifier;
    }

    /**
     * Returns the filter's data descriptions
     *
     * @return tx_ptlist_dataDescriptionCollection
     */
    public function get_dataDescriptions() {
        return $this->dataDescriptions;
    }

    public function set_dataDescriptions(tx_ptlist_dataDescriptionCollection $dataDescriptions) {
        $this->dataDescriptions = $dataDescriptions;
    }

    public function get_hasUserInterface() {
        return $this->hasUserInterface;
    }

    public function set_label($label) {
        $this->label = $label;
    }

    public function set_submitLabel($submitLabel) {
    	$this->submitLabel = $submitLabel;
    }

    public function get_isActive() {
        return $this->isActive;
    }

    public function set_isActive($isActive) {
        $this->isActive = (boolean)$isActive;
    }

    public function get_value() {
        return $this->value;
    }

    public function set_value($value) {
        $this->value = $value;
    }

    public function get_dependsOn() {
    	return $this->dependsOn;
    }

    public function get_hideColumns() {
    	return $this->hideColumns;
    }

	public function get_invert() {
		return $this->invert;
	}



}



?>