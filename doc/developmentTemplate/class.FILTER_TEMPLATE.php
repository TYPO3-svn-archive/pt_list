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
 * This is a template file for developing new filter-classes for pt_list.
 * To implement your own filter, follow these steps:
 * 
 * 1. Copy the this file into the /controller/filter/ directory of pt_list (or whatever extension you are working on)
 * 2. Rename the file to class.tx_<condensedExtKey>_controller_filter_<yourFilterName>.php
 *    Rename the class name to tx_<condensedExtKey>_controller_filter_<yourFilterName>
 * 3. Add an alias in /typoscript/static/_default/plugin.tx_ptlist.alias.ts which looks like that:
 *    filter_<yourFilterName> = EXT:pt_list/controller/filter/class.tx_ptlist_controller_filter_<yourFilterName>.php:tx_ptlist_controller_filter_<yourFilterName>
 * 4. Implement the class methods as described in the comments.
 * 5. Add a new view for your filter and save it as /view/filter/<yourFilterName>/class.tx_<condensedExtKey>_view_filter_<yourFilterName>_userInterface.php
 *    The view is an empty class that extends tx_ptmvc_view. Here is an example for copy & paste:
 * 
 *      <?php
 *      require_once t3lib_extMgm::extPath('pt_mvc').'classes/class.tx_ptmvc_view.php';
 *      
 *      class tx_ptlist_view_filter_<yourFilterName>_userInterface extends tx_ptmvc_view {
 *   
 *      }
 *      ?>
 *    
 * 5. Make sure to add some documentation in /doc/DevDoc.txt and /doc/manual.sxw if it's a new pt_list filter!
 * 
 */



/** 
 * ####Feel free to add some comments on this file####
 *
 * $Id:$
 *
 * @author  ###Here goes your name + email####
 * @since   ###Since when did you work on this file####
 */ 



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_filter.php';   // The parent filter-controller class

// ####Implement this view class and include it here, if your filter has an user interface!####
#require_once t3lib_extMgm::extPath('pt_list').'view/filter/<yourFilterName>/class.tx_ptlist_view_filter_<yourFilterName>_userInterface.php';



/**
 * ####Feel free to add some comments on your filter class####
 *
 * @author      ###Here goes your name + email####
 * @since       ###Since when did you work on this file####
 * @package     TYPO3
 * @subpackage  tx_ptlist
 */
class tx_ptlist_controller_filter_yourFilterName extends tx_ptlist_filter {
	
    /***************************************************************************
     * Methods defined in parent abstract class "tx_ptlist_filter" 
     **************************************************************************/
    
    
    /**
     * ###Here comes the main logic of your filter: 
     * generate a SQL snippet according to your filter input
     * Make sure to avoid any SQL injections here!####
     * 
     * Returns the SQL WHERE clause snippet for this filter
     * +++++ IMPORTANT: avoid SQL injections in your implementation!!! +++++
     *
     * @param   void
     * @return  string HTML output
     * @author  ###Here goes your name + email####
     * @since   ###Since when did you work on this file####
     */
    public function getSqlWhereClauseSnippet() {
        
        # $sqlWhereClauseSnippet = '1 = 1';
        #return $sqlWhereClauseSnippet;
        
    }
    
    
    
    /**
     * Only implement this function, if you have any
     * validation logic. This function 
     * returns 'true' by default!
     * 
     * @param   void
     * @return  boolean     Returns true, if filter validates
     * @author  ###Here goes your name + email####
     * @since   ###Since when did you work on this file####
     */
    #public function validate() {
    #	
    #}
    
    
    
    /***************************************************************************
     * Template methods
     **************************************************************************/
    
    /**
     * ###Feel free to add all functionality here that has to be executed 
     * when the filter frontend form is submitted. In most cases,
     * this is  storing the values from the filter-frontend-form to the "value" 
     * property of the filter.
     * 
     * If your filter values need any validation, put it into a 
     * method called "validate" and return 'false' if your filter input
     * does not validate####
     *
     * @param   void
     * @return  void
     * @author  ###Here goes your name + email####
     * @since   ###Since when did you work on this file####
     */
    public function preSubmit() {
        
        #$this->value = $this->params['value'];
        
    }
    
    
    
    /***************************************************************************
     * Controller action methods 
     **************************************************************************/
    
    /**
     * Displays the user interface in active state.
     *
     * @param   void
     * @return  string HTML output
     * @author  ###Here goes your name + email####
     * @since   ###Since when did you work on this file####
     */
    public function isActiveAction() {
        
        // default case: no difference between 'isActive' and 'isNotActive'
        #return $this->doAction('isNotActive');
        
    }
    
    
    
    /**
     * Displays the user interface in inactive state
     *
     * @param   void
     * @return  string HTML output
     * @author  ###Here goes your name + email####
     * @since   ###Since when did you work on this file####
     */
    public function isNotActiveAction() {
        
        #$view = $this->getView($this->getFilterViewName());
        #$view->addItem($this->value, 'value');
        #return $view->render();
        
    }
    
    
    
    /***************************************************************************
     * Helper methods 
     **************************************************************************/
    
    /**
     * Returns the view name for this filter
     *
     * @return  string      View name of this filter in PEAR-convention
     * @author  ###Here goes your name + email####
     * @since   ###Since when did you work on this file####
     */
    protected function getFilterViewName() {
    	
    	#return 'filter_<yourFilterName>_userInterface';
    	
    }
    
    
}

?>