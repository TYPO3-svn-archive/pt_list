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
 * Class file definition for timespan filter
 *
 * @author   Michael Knoll
 * @since    2009-07-17
 * @version  $ID:$
 */



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_filter.php';
require_once t3lib_extMgm::extPath('pt_list').'view/filter/timeSpan2/class.tx_ptlist_view_filter_timeSpan2_userInterface.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/objects/class.tx_pttools_exception.php';



/**
 * Class implementing a "timespan" filter
 *
 * @package     TYPO3
 * @subpackage  pt_list\controller\filter
 * @author		Michael Knoll <knoll@punkt.de>
 * @since		2009-07-17
 */
class tx_ptlist_controller_filter_timeSpan2 extends tx_ptlist_filter {



	/**
	 * Current state
	 * 'from' => [<empty, if mode is "preset>|<timestamp>]
	 * 'to' => [<empty, if mode is "preset>|<timestamp>]
	 *
	 * @var array	current filter value
	 */
	protected $value = array();



	/****************************************************************************************************************
     * Modifying MVC functionality
     ****************************************************************************************************************/

    /**
     * MVC init method:
     * Checks if the column collection contains exactly one column as this filter can be used only with one column at the same time
     *
     * @param   void
     * @return  void
     * @throws  tx_pttools_exceptionAssertion   if more than one column is attached to the filters columnCollection
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-01-23
     */
    public function init() {

    	// Make sure, pt_jqueryui extension is installed
        if (!t3lib_extMgm::isLoaded('pt_jqueryui')) {
            throw new tx_pttools_exception('You need to install and load pt_jqueryui to run datepicker filter!');
        }

        parent::init();
        tx_pttools_assert::isEqual(count($this->dataDescriptions), 1, array('message' => sprintf('This filter can only be used with 1 dataDescription (dataDescription found: "%s"', count($this->dataDescriptions))));
    }



	/****************************************************************************************************************
	 * Action Methods
	 ****************************************************************************************************************/

	/**
	 * Displays the user interface in active state
	 * - calls isNotActiveAction as both states have the same output
	 *
	 * @param 	void
	 * @return 	string HTML output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	public function isActiveAction() {
		// in this case we redirect to the "isActive" action as we do not want a different interface when the filter is active
		return $this->doAction('isNotActive');
	}



	/**
	 * Is not active action
	 *
	 * @param 	void
	 * @return 	string	HTML output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-01
	 */
	public function isNotActiveAction() {
		$view = $this->getView('filter_timeSpan2_userInterface');
		$view->addItem($this->value, 'value');
		$view->addItem($this->conf, 'filterconf');
		return $view->render();

	}



	/**
	 * This method will be called to generate the output for the filter breadcrumb.
	 * If you want additional functionality or a different output overwrite this method.
	 *
	 * @param 	void
	 * @return 	string HTML ouput
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-06
	 */
	public function breadcrumbAction() {

		if (!empty($this->value)) {
			// TODO ry21 add some localization here!
			$value = $this->formatTimeSpan($this->value['from'], $this->value['to']);
		} else {
			$value = 'Not set';
		}

		$view = $this->getView('filter_breadcrumb');
		$view->addItem($this->label, 'label');
		$view->addItem($value, 'value');
		return $view->render();
	}



    /****************************************************************************************************************
     * Template methods
     ****************************************************************************************************************/

    /**
     * Pre-Submit action
     *
     * @param   void
     * @return  string  HTML output
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-02-27
     */
    public function preSubmit() {

        // save the incoming parameters to your value property here
        $this->value = array(
            'from' => $this->params['from'],
            'to' => $this->params['to'],
        );

    }



	/****************************************************************************************************************
     * Domain Logic -
     * implementing abstract methods from parent class
     ****************************************************************************************************************/

    /**
     * Get sql where clause snippet
     *
     * @param   array   (optional) if empty the function takes $this->value as  $value
     * @return  string  sql where clause snippet
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-07-17
     */
    public function getSqlWhereClauseSnippet() {

        $span = $this->valueToTimeSpan($this->value);

        if (empty($span['from']) && empty($span['to'])) {
            throw new tx_pttools_exception('"From" and "to" cannot be both empty!');
        }

        $rangeSnippet = $this->getRangeSnippet($span['from'], $span['to'], $this->getDbColumn());

        return $rangeSnippet;
    }



	/****************************************************************************************************************
     * Helper methods
     ****************************************************************************************************************/

	/**
	 * Format timespan
	 *
	 * @param 	int		'from' timestamp
	 * @param 	int		'to' timestamp
	 * @return 	string	formatted timespan string
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-09
	 */
	protected function formatTimeSpan($from, $to) {

		// Convert to Unix timestamp
		$fromDateTime = date_create($from);
        $from = date_format($fromDateTime, 'U');
        $toDateTime = date_create($to);
        $to = date_format($toDateTime, 'U');

		if (date('Y', $from) == date('Y', $to)) {
			if (date('m', $from) == date('m', $to)) {
				if (date('d', $from) == date('d', $to)) {
					// same (single) day
					$value = date('d.m.y', $from);
				} else {
					// different day, but same month
					$value = date('d.', $from) . '-' . date('d.m.y', $to);
				}
			} else {
				// different day and different month, but same year
				$value = date('d.m.', $from) . '-' . date('d.m.y', $to);
			}
		} else {
			// completely different
			$value = date('d.m.y', $from) . '-' . date('d.m.y', $to);
		}
		return $value;
	}



	/**
	 * Get sql where clause snippet
	 *
	 * @param 	array 	(optional) if empty the function takes $this->value as  $value
	 * @return 	string 	sql where clause snippet
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>, Michael Knoll <knoll@punkt.de>
	 * @since	2009-07-17
	 */
	protected function getRangeSnippet($from, $to, $dbColumn) {

		// Check for corectness of parameters
        if (empty($from) && empty($to)) {
            throw new tx_pttools_exception('"From" and "to" cannot be both empty!');
        }

        // Determine field type of date field
		$dateFieldType = $this->conf['dateFieldType'] == '' ? 'timestamp' : $this->conf['dateFieldType'];
        $sqlWhereClauseSnippet = array();
        $snippet = '';

		if ($dateFieldType == 'date') {
	        // Generate where clause for date fields
	        $dateFrom = date('Y-m-d', $from);
	        $dateTo   = date('Y-m-d', $to);
	        $snippet  = $dbColumn . ' BETWEEN \'' . $dateFrom . '\' AND \'' . $dateTo .'\'';
		} elseif($dateFieldType == 'timestamp') {
            // Generate where clause for timestamp fields
	        if (!empty($from)) {
	            $sqlWhereClauseSnippet[] = $dbColumn.' >= '.intval($from);
	        }
	        if (!empty($to)) {
	            $sqlWhereClauseSnippet[] = $dbColumn.' <= '.intval($to);
	        }
	        $snippet = implode(' AND ', $sqlWhereClauseSnippet);
		} else {
			throw new tx_pttools_exceptionConfiguration("No valid date field type set.", "No valid date field type set for timespan filter. Type was $dateFieldType but can only be 'date' or 'timestamp'!");
		}

        return $snippet;
    }



    /**
     * Returns the DB column for the filter to be applied to
     *
     * @return string
     * @author Fabrizio Branca <mail@fabriziobranca.de>
     * @since 2009-07-17
     */
    protected function getDbColumn() {
        $table = $this->dataDescriptions->getItemByIndex(0)->get_table();
        $field = $this->dataDescriptions->getItemByIndex(0)->get_field();

        return $table.'.'.$field;
    }



    /**
     * Converts filter values to an array of unix timestamps
     *
     * @param   array   $value  Array with dates array( 'from' => date, 'to' => date )
     * @return  array           Array with timestamps array( 'from' => timestamp, 'to' => timestamp )
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-07-17
     */
    protected function valueToTimeSpan($value) {

    	tx_pttools_assert::isNotEmpty($value['from'], array('message' => 'Value "From" must not be empty but was empty.'));
    	tx_pttools_assert::isNotEmpty($value['to'], array('message' => 'Value "To" must not be empty but was empty.'));

    	$fromDateTime = date_create($value['from']);
    	$fromTimestamp = date_format($fromDateTime, 'U');

    	$toDateTime = date_create($value['to']);
    	$toTimestamp = date_format($toDateTime, 'U');

    	return array('from' => $fromTimestamp, 'to' => $toTimestamp);
    }


}

?>