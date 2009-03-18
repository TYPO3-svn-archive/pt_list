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
 * String filter controller class for the 'pt_list' extension
 *
 * $Id: class.tx_ptlist_controller_filter_string.php,v 1.3 2009/02/02 13:15:16 ry44 Exp $
 *
 * @author  Rainer Kuhn <kuhn@punkt.de>
 * @since   2009-01-20
 */ 



require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_filter.php';
require_once t3lib_extMgm::extPath('pt_list').'view/filter/string/class.tx_ptlist_view_filter_string_userInterface.php';



/**
 * String filter controller
 *
 * @author      Rainer Kuhn <kuhn@punkt.de>
 * @since       2009-01-20
 * @package     TYPO3
 * @subpackage  tx_ptlist
 */
class tx_ptlist_controller_filter_string extends tx_ptlist_filter {
    
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
     * @since   2009-01-20
     */
    public function getSqlWhereClauseSnippet() {
        
        $sqlWhereClauseSnippets = array();
        
        foreach ($this->dataDescriptions as $dataDescription) {  /* @var $dataDescription tx_ptlist_dataDescription */
            $sqlWhereClauseSnippets[] = sprintf('%s.%s LIKE "%%%s%%"', $dataDescription->get_table(), $dataDescription->get_field(), $GLOBALS['TYPO3_DB']->quoteStr($this->value, $dataDescription->get_table()));
        }
        $sqlWhereClauseSnippet = implode(' OR ', $sqlWhereClauseSnippets);
        
        return $sqlWhereClauseSnippet;
        
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
     * @since   2009-01-20
     */
	public function submitAction() {
	    
		$this->isActive = true;
		$this->value = $this->params['value'];
		return $this->doAction('default');
		
	}
    
    /**
     * Displays the user interface in active state (calls isNotActiveAction in this case)
     *
     * @param   void
     * @return  string HTML output
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-20
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
        
        $view = $this->getView('filter_string_userInterface');
        $view->addItem($this->value, 'value');
        return $view->render();
        
    }
	
	
	
}

?>