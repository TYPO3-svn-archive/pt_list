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
 * Class definition file for time span filter
 * 
 * @version     $Id$
 * @author      Fabrizio Branca <mail@fabrizio-branca.de>
 * @since       2009-01-26
 */



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_filter.php';
require_once t3lib_extMgm::extPath('pt_list').'view/filter/timeSpan/class.tx_ptlist_view_filter_timeSpan_userInterface.php';



/**
 * Class implementing a "minimum" filter
 *
 * @author		Fabrizio Branca <mail@fabrizio-branca.de>
 * @since		2009-01-26
 * @package     typo3
 * @subpackage  pt_list
 */
class tx_ptlist_controller_filter_timeSpan extends tx_ptlist_filter {

	/**
	 * Current state
	 * 'mode' => ['custom'|'preset'],
	 * 'preset' => [<empty, if mode is "custom">|'today'|'yesterday'|'['this'|'last']['week'|'month'|'year']']
	 * 'from' => [<empty, if mode is "preset>|<timestamp>]
	 * 'to' => [<empty, if mode is "preset>|<timestamp>]
	 *
	 * @var mixed	current filter value
	 */
	protected $value = array();

	
	
	/***************************************************************************
     * Methods modifying standard MVC functionality 
     **************************************************************************/

	/**
	 * MVC init method:
	 * Checks if the column collection contains exactly one column as this filter can be used only with one column at the same time
	 *
	 * @param 	void
	 * @return 	void
	 * @throws	tx_pttools_exceptionAssertion	if more than one column is attached to the filters columnCollection
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-23
	 */
	public function init() {
		parent::init();
		tx_pttools_assert::isEqual(count($this->dataDescriptions), 1, array('message' => sprintf('This filter can only be used with 1 dataDescription (dataDescription found: "%s"', count($this->dataDescriptions))));
	}

	
	
	/***************************************************************************
     * Action methods 
     **************************************************************************/
	
	/**
	 * Displays the user interface in active state
	 * - calls isNotActiveAction
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
		$view = $this->getView('filter_timeSpan_userInterface');
		$view->addItem($this->value, 'value');

		$spans = $this->getPresetTimeSpans();

		foreach ($spans as &$span) {
			$span['quantity'] = $this->getRowCountForTimeSpan($span['from'], $span['to']);
			$span['formattedTimeSpan'] = $this->formatTimeSpan($span['from'], $span['to']);
		}

		$view->addItem($spans, 'spans');
		$view->addItem($this->conf['enableCustomDates'], 'customDates');
		
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
			$span = $this->valueToTimeSpan($this->value);
			$value = $this->formatTimeSpan($span['from'], $span['to']);
		} else {
			$value = 'Not set';
		}

		$view = $this->getView('filter_breadcrumb');
		$view->addItem($this->label, 'label');
		$view->addItem($value, 'value');
		return $view->render();
	}
	
	
	
	/***************************************************************************
     * Template methods
     **************************************************************************/
	
    /**
     * Pre-Submit action
     *
     * @param   void
     * @return  string  HTML output
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>, Michael Knoll <knoll@punkt.de>
     * @since   2009-02-27
     */
    protected function preSubmit() {

        // save the incoming parameters to your value property here
        if ($this->params['value'] == 'custom') {
            $this->value = array(
                'mode' => 'custom',
                'from' => $this->params['from'],
                'to' => $this->params['to'],
            );
        } else {
            $this->value = array(
                'mode' => 'preset',
                'preset' => $this->params['value'],
            );
        }

    }

	
    
    /***************************************************************************
     * Domain Logic - 
     * Methods implementing abstract methods from "tx_ptlist_filter" 
     **************************************************************************/

	/**
	 * Get sql where clause snippet
	 *
	 * @param 	array 	(optional) if empty the function takes $this->value as  $value
	 * @return 	string 	sql where clause snippet
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-09
	 */
	public function getSqlWhereClauseSnippet() {

		$span = $this->valueToTimeSpan($this->value);

		if (empty($span['from']) && empty($span['to'])) {
			throw new tx_pttools_exception('"From" and "to" cannot be both empty!');
		}

        return $this->getRangeSnippet($span['from'], $span['to']);
    }
    

    
    /**
     * Get sql where clause snippet
     *
     * @param   array   (optional) if empty the function takes $this->value as  $value
     * @return  string  sql where clause snippet
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-02-09
     */
    protected function getRangeSnippet($from, $to) {

    	$dbColumn = $this->getDbColumn();
    	
        if (empty($from) && empty($to)) {
            throw new tx_pttools_exception('"From" and "to" cannot be both empty!');
        }

        $sqlWhereClauseSnippet = array();
        if (!empty($from)) {
            $sqlWhereClauseSnippet[] = $dbColumn.' >= '.intval($from);
        }
        if (!empty($to)) {
            $sqlWhereClauseSnippet[] = $dbColumn.' <= '.intval($to);
        }
        return implode(' AND ', $sqlWhereClauseSnippet);
    }
    
    
    
    /**
     * Returns 'table.field' string for data description on which filter is working on
     *
     * @return string   'table.field' string
     * @author Fabrizio Branca <mail@fabrizio-branca.de>
     * @since  2009-02-09
     */
    protected function getDbColumn() {
        $table = $this->dataDescriptions->getItemByIndex(0)->get_table();
        $field = $this->dataDescriptions->getItemByIndex(0)->get_field();

        return $table.'.'.$field;
    }
    
    
    
    /**
     * Returns the amount of rows found for a given timespan
     *
     * @param   int     "from" timestamp
     * @param   int     "to" timestamp
     * @return  int     amount of rows
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-02-09
     */
    protected function getRowCountForTimeSpan($from, $to) {
        // retrieve list object from regitry
        $listObject = tx_pttools_registry::getInstance()->get($this->listIdentifier.'_listObject'); /* @var $listObject tx_ptlist_list */

        // prepare parameters for the "getGroupData" call
        $select = 'count(*) as quantity';

        // where
        $where = $this->getRangeSnippet($from, $to, $this->getDbColumn());

        // ignore filters from configuration
        $ignoredFiltersForWhereClause = $this->conf['ignoreFilters'];

        // apend itself to the list
        $ignoredFiltersForWhereClause .= empty($ignoredFiltersForWhereClause) ? '' : ', ';
        $ignoredFiltersForWhereClause .= $this->filterIdentifier;

        $data = $listObject->getGroupData($select, $where, '', '', '', $ignoredFiltersForWhereClause);

        return $data[0]['quantity'];
    }
    
    
    
    /**
     * Return an array with "from" and "to" keys for a given valueArray
     *
     * @param   array   value array
     * @return  array   array('from' => <fromTimestamp>, 'to' => <toTimestamp>);
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-02-09
     */
    protected function valueToTimeSpan(array $valueArray) {
        if ($valueArray['mode'] == 'custom') {
            $from = $valueArray['from'];
            $to = $valueArray['to'];
        } elseif ($valueArray['mode'] == 'preset') {
            $spans = $this->getPresetTimeSpans();
            if (!empty($spans[$valueArray['preset']])) {
                $from = $spans[$valueArray['preset']]['from'];
                $to = $spans[$valueArray['preset']]['to'];
            } else {
                throw new tx_pttools_exception('Invalid preset!');
            }
        } else {
            throw new tx_pttools_exception('Invalid mode!');
        }
        if (empty($from) && empty($to)) {
            throw new tx_pttools_exception('"From" and "to" cannot be both empty!');
        }
        return array('from' => $from, 'to' => $to);
    }



    /**
     * Get preset timespans
     *
     * @param   void
     * @return  array   array of arrays with 'value', 'label', 'from' and 'to' keys
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-02-09
     */
    protected function getPresetTimeSpans() {

        /**
         * This method is called outside the controller context, so the configuration may not be loaded at this point.
         * But as we need the configuration here, we load it by ourselves
         */
        if (empty($this->conf)) {
            $this->getConfiguration();
        }

        tx_pttools_assert::isNotEmptyString($this->conf['spans'], array('message' => 'No "spans" configuration found!'));

        $oneMinute = 60;
        $oneHour = 60 * $oneMinute;
        $oneDay = 24 * $oneHour;
        $oneWeek = 7 * $oneDay;
        // $oneMonth = <depends on the month! :)>

        $spans = array();
        foreach (t3lib_div::trimExplode(',', $this->conf['spans']) as $span) {
            switch ($span) {
                case 'today': {
                    $spans[$span] = array(
                        'value' => $span,
                        'label' => 'Today',
                        'from' => mktime(0, 0, 0, date('m'), date('d')),
                    );
                    $spans[$span]['to'] = $spans[$span]['from'] + $oneDay - 1;
                } break;

                case 'thisweek': {
                    $spans[$span] = array(
                        'value' => $span,
                        'label' => 'This week',
                        'from' => strtotime('last monday') + (date('w')==='1' ? $oneWeek : 0), // if today is monday "last monday" returns the last monday :)
                    );
                    $spans[$span]['to'] = $spans[$span]['from'] + $oneWeek - 1;
                } break;

                case 'thismonth': {
                    $spans[$span] = array(
                        'value' => $span,
                        'label' => 'This month',
                        'from' => mktime(0, 0, 0, date('m'), 1),
                        'to' => mktime(0, 0, 0, date('m')+1, 1)-1
                    );
                } break;

                case 'thisyear': {
                    $spans[$span] = array(
                        'value' => $span,
                        'label' => 'This year',
                        'from' => mktime(0, 0, 0, 1, 1),
                        'to' => mktime(0, 0, 0, 1, 1, date('Y')+1)-1
                    );
                } break;

                case 'yesterday' : {
                    $spans[$span] = array(
                        'value' => $span,
                        'label' => 'Yesterday',
                        'from' => mktime(0, 0, 0, date('m'), date('d')-1),
                        'to' => mktime(0, 0, 0, date('m'), date('d'))-1,
                    );
                } break;

                case 'lastweek': {
                    $spans[$span] = array(
                        'value' => $span,
                        'label' => 'Last week',
                        'from' => strtotime('last monday') - $oneWeek + (date('w')==='1' ? $oneWeek : 0), // if today is monday "last monday" returns the last monday :)
                    );
                    $spans[$span]['to'] = $spans[$span]['from'] + $oneWeek - 1;
                } break;

                case 'lastmonth': {
                    $spans[$span] = array(
                        'value' => $span,
                        'label' => 'Last month',
                        'from' => mktime(0, 0, 0, date('m')-1, 1),
                        'to' => mktime(0, 0, 0, date('m'), 1)-1
                    );
                } break;

                case 'lastyear': {
                    $spans[$span] = array(
                        'value' => $span,
                        'label' => 'Last year',
                        'from' => mktime(0, 0, 0, 1, 1, date('Y')-1),
                        'to' => mktime(0, 0, 0, 1, 1, date('Y'))-1
                    );
                } break;

            }

        }
        return $spans;
    }
    
    
    
    /***************************************************************************
     * Helper methods 
     **************************************************************************/
    
    /**
     * Format timespan
     *
     * @param   int     'from' timestamp
     * @param   int     'to' timestamp
     * @return  string  formatted timespan string
     * @author  Fabrizio Branca <mail@fabrizio-branca.de>
     * @since   2009-02-09
     */
    protected function formatTimeSpan($from, $to) {
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

}

?>