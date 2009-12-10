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
 * $Id$
 *
 * @author  Rainer Kuhn <kuhn@punkt.de>, Michael Knoll <knoll@punkt.de>
 * @since   2009-01-20
 */ 



/**
 * Inclusion of external ressources
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
        if ($this->value == '') {
            return ' 1 ';
        }        
        foreach ($this->dataDescriptions as $dataDescription) {  /* @var $dataDescription tx_ptlist_dataDescription */
            $sqlWhereClauseSnippetsAndParts = array();
            foreach (t3lib_div::trimExplode(' ', $this->value, true) as $part) {
                $sqlWhereClauseSnippetsAndParts[] = sprintf('%s LIKE "%%%s%%"', $dataDescription->getSelectClause(false), $GLOBALS['TYPO3_DB']->quoteStr($part, $dataDescription->get_table()));
            }
            $sqlWhereClauseSnippets[] = ' ( ' . implode(' AND ', $sqlWhereClauseSnippetsAndParts) . ' ) ';
        }
        $sqlWhereClauseSnippet = implode(' OR ', $sqlWhereClauseSnippets);
        
        return $sqlWhereClauseSnippet;
        
    }
    
    
    
    /***************************************************************************
     * Template methods
     **************************************************************************/
    
    /**
     * Method implementing all stuff that needs to be done BEFORE submit action is 
     * run. This is a template method!
     *
     * @param   void
     * @return  void
     * @author  Michael Knoll
     * @since   2009-09-23
     */
    public function preSubmit() {
        
        $this->value = $this->params['value'];
        
    }

    
    
    /***************************************************************************
     * Controller action methods 
     **************************************************************************/
    
    /**
     * Displays the user interface in active state.
     *
     * @param   void
     * @return  string HTML output
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2009-01-20
     */
    public function isActiveAction() {
        
        // default case: no difference between 'isActive' and 'isNotActive'
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
