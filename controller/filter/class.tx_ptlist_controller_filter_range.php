<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2009 Rainer Kuhn (kuhn@punkt.de)
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
 * Range filter controller class for the 'pt_list' extension
 *
 * $Id$
 *
 * @author  Rainer Kuhn <kuhn@punkt.de>
 * @since   2009-01-23
 */ 



require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_filter.php';
require_once t3lib_extMgm::extPath('pt_list').'view/filter/range/class.tx_ptlist_view_filter_range_userInterface.php';



/**
 * Range filter controller
 *
 * @author      Rainer Kuhn <kuhn@punkt.de>
 * @since       2009-01-23
 * @package     TYPO3
 * @subpackage  tx_ptlist
 */
class tx_ptlist_controller_filter_range extends tx_ptlist_filter {
    
    /***************************************************************************
     * Methods defined in parent abstract class "tx_ptlist_filter" 
     **************************************************************************/
    
    
    /**
     * Returns the SQL WHERE clause snippet for this filter
     * +++++ IMPORTANT: avoid SQL injections in your implementation!!! +++++
     *
     * @param   void
     * @return  string HTML output
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-23
     */
    public function getSqlWhereClauseSnippet() {
        
        tx_pttools_assert::isEqual(count($this->dataDescriptions), 1, array('message' => 'This filter can only be used with 1 column'));
        
        $table = $this->dataDescriptions->getItemByIndex(0)->get_table();
        $field = $this->dataDescriptions->getItemByIndex(0)->get_field();
        
        $dbColumn = $table.'.'.$field;
        
        $sqlWhereClauseSnippet = array();
        if (!empty($this->value['minval'])) {
        	$sqlWhereClauseSnippet[] = $dbColumn.' >= '.intval($this->value['minval']);
        }
        if (!empty($this->value['maxval'])) {
        	$sqlWhereClauseSnippet[] = $dbColumn.' <= '.intval($this->value['maxval']);
        }
        return implode(' AND ', $sqlWhereClauseSnippet);
        
    }
    
    
    
    /***************************************************************************
     * Controller action methods 
     **************************************************************************/
	
    /**
     * Processes the filter form submission
     *
     * @param   void
     * @return  string HTML output
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-23
     */
	public function submitAction() {
        $this->value = array(
        	'minval' => $this->params['minval'],
        	'maxval' => $this->params['maxval']
        );
		return parent::submitAction();
		
	}
    
    /**
     * Displays the user interface in active state (calls isNotActiveAction in this case)
     *
     * @param   void
     * @return  string HTML output
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-23
     */
    public function isActiveAction() {
        
        // in this case we redirect to the "isActive" action as we do not want a different interface when the filter is active
        return $this->doAction('isNotActive');
        
    }
    
    /**
     * Displays the user interface in inactive state
     *
     * @param   void
     * @return  string HTML output
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-20
     */
    public function isNotActiveAction() {
        
        $view = $this->getView('filter_range_userInterface');
        $view->addItem($this->value['minval'], 'minval');
        $view->addItem($this->value['maxval'], 'maxval');
        return $view->render();
        
    }
    
    
    public function validate() {
    	if (TYPO3_DLOG) t3lib_div::devLog('Range validation', 'pt_list', 1, $this->value);
    	
    	// at least one of the values has to be set
    	if (empty($this->value['minval']) && empty($this->value['maxval'])) return false;
    	
    	// both values have to be numeric if they are not empty
    	if (!empty($this->value['minval']) && !is_numeric($this->value['minval'])) return false;
    	if (!empty($this->value['maxval']) && !is_numeric($this->value['maxval'])) return false;
    	
    	// if both are set, the max value has to be higher than the min value
    	if (!empty($this->value['minval']) && !empty($this->value['maxval'])) {
    		if ($this->value['maxval'] < $this->value['minval']) return false;
    	}
    	
    	return true;
    }



	/**
	 * This method will be called to generate the output for the filter breadcrumb.
	 *
	 * @param 	void
	 * @return 	string HTML ouput
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-06
	 */
	public function breadcrumbAction() {

		if (empty($this->value)) {
			$value = 'Not set';
		} else {
			$value = sprintf('%s-%s', $this->value['minval'], $this->value['maxval']);
		}

		$view = $this->getView('filter_breadcrumb');
		$view->addItem($this->label, 'label');
		$view->addItem($value, 'value');
		return $view->render();
	}
	
	
}

?>