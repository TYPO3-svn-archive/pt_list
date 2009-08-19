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
 * Class definition for pt_list structured list view.
 * 
 * @version   $Id$
 * @author    Michael Knoll <knoll@punkt.de>
 * @since     2009-06-30
 */



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_list').'view/list/class.tx_ptlist_view_list_itemList.php';
require_once t3lib_extMgm::extPath('pt_tools').'res/staticlib/class.tx_pttools_assert.php';



/**
 * Class definition for structured list view.
 * 
 * @package Typo3
 * @subpackage pt_list
 * @author Michael Knoll <knoll@punkt.de>
 * @since 2009-06-30
 */
class tx_ptlist_view_list_itemList_structured extends tx_ptlist_view_list_itemList {

	
	
	/**
	 * @var array  Columns to structure list by
	 */
	protected $structureByCols;
	
	
	
	/**
	 * @var array  Columns to show as header for structured list
	 */
	protected $structureByHeaders;
	
	
	/**
	 * @var string ID of current list
	 */
	protected $listId;
	
	
	/**
	 * @var string String to concat structured header columns
	 */
	protected $concatString;
	
	
	/**
	 * Constructor for structured list
	 * 
	 * @author Michael Knoll <knoll@punkt.de>
	 * @since 2009-06-30
	 * @param  $controller tx_ptmvc_controller
	 * @return void
	 */
	public function __construct($controller=NULL) {
		
		// TODO ry21: Make this configurable via TS
		$this->templateFilePath = t3lib_extMgm::extPath('pt_list') . 'template/list/list_itemList.tpl';
		parent::__construct($controller);
		
	}
	
	
	
    /**
     * This will be executed before rendering the template
     * 
     * Create template configuration for structured lists and sort data according to structure given in TS
     * 
     * @param   void
     * @return  void
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-06-30
     */
    public function beforeRendering() {
        parent::beforeRendering();
        
        /* Get configuration for structured list */
        $this->initStructureConfig();
        
        /* Generate keys for sorting list items */
        $this->createSortingColumn();
        
        /* Sort list items according to structure */
        $this->reSortListItems();
        
        /* Add headers for structured sections */
        $this->addStructureHeaders();
        
        /* Assign additional template vars for structured list */
        $this->addItem('1', 'is_a_structured_list', false);
        $this->addItem(array_merge($this->structureByCols, $this->structureByHeaders), 'structure_by_cols', false);
        $this->addItem($this->structureByHeaders, 'structure_by_headers', false);
        $this->addItem($this->countVisibleCols(array_keys($this->itemsArr['columns']),array_merge($this->structureByCols, $this->structureByHeaders)), 'spanned_cols_by_header', false);
        $this->addItem($this->concatString, 'concat_string');
    }
    
    
    
    /* ***************************************************************
     * HELPER METHODS
     * ***************************************************************/
    
    
    
    /**
     * Helper method for generating sorting column for structure
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-07-01
     */
    protected function createSortingColumn() {
        foreach ($this->itemsArr['listItems'] as &$row) {
            $combinedStructCol = $this->combineCols($row, $this->structureByCols);
            $row['__combined_struct_col__'] = $combinedStructCol;
        }  
    }
    
    
    
    /**
     * Helper method for initializing structure config 
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-07-01
     */
    protected function initStructureConfig() {
        $this->listId               = $this->itemsArr['listIdentifier'];
        $this->structureByCols      = t3lib_div::trimExplode(',', tx_pttools_div::getTS('plugin.tx_ptlist.listConfig.' . $this->listId . '.structureByCols'));
        tx_pttools_assert::isArray($this->structureByCols, array('message' => 'No structure by cols given for list configuration!'));
        $this->structureByHeaders   = t3lib_div::trimExplode(',', tx_pttools_div::getTS('plugin.tx_ptlist.listConfig.' . $this->listId . '.structureByHeaders'));
        tx_pttools_assert::isArray($this->structureByHeaders, array('message' => 'No headers for structure by col given for list configuration!'));
        
        $this->concatString = tx_pttools_div::getTS('plugin.tx_ptlist.listConfig.' . $this->listId . '.concatString') != '' ?
            tx_pttools_div::getTS('plugin.tx_ptlist.listConfig.' . $this->listId . '.concatString') : ' - ';
    }
    
    
    
    /**
     * Helper method for generating structure headers
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-07-01
     */
    protected function addStructureHeaders() {
    	$structCol = '';
        $newListItems = array();
        foreach ($this->itemsArr['listItems'] as $row) {
            $currentStructCols = $row['__combined_struct_col__'];
            if ($currentStructCols != $structCol) {
                $structCol = $currentStructCols;
                /**
                 * Use '_' to concat header here to make sorting of concatenated headers correct
                 * Add $row in header row display correct headers for structured list
                 */
                $newListItems[] = array_merge(
	                                  array(
	                                      'is_structure_header' => '1', 
	                                      '__structure_header__' => $this->getCurrentHeader($row, $this->structureByHeaders, $this->concatString)
	                                  ), 
	                                  $row
                                  );
            }
            $newListItems[] = $row;
        }
        $this->itemsArr['listItems'] = $newListItems;
    }
    
    
    
    /**
     * Helper method for re-sorting list items according to structure by columns
     * 
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-07-01
     */
    protected function reSortListItems() {
    	$combinedStructKeys = $this->getArrayKeys($this->itemsArr['listItems'], '__combined_struct_col__');
    	$secondKeyName = $this->_extConf['listConfig.'][$this->listId . '.']['defaults.']['sortingColumn'];
        $secondKey = $this->getArrayKeys($this->itemsArr['listItems'], $secondKeyName);
        $sortingDirection = $this->_extConf['listConfig.'][$this->listId . '.']['defaults.']['sortingDirection'] == 'DESC' ? SORT_DESC : SORT_ASC;
        
        array_multisort($combinedStructKeys, SORT_ASC, $secondKey, $sortingDirection, $this->itemsArr['listItems']);
    }
    
    
    
    /**
     * Determines the number of visible columns for a given list.
     * 
     * @param   array   $allColumns     Columns array containing all columns
     * @param   array   $hiddenColumns  Array of columns to hide
     * @return  int                     Number of columns to display
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-07-01
     */
    protected function countVisibleCols($allColumns, $hiddenColumns) {
    	$counter = 0;
    	foreach ($allColumns as $column) {
    		if (!in_array($column, $hiddenColumns)) {
    			$counter++;
    		}
    	}
    	return $counter;
    }
    
    
    
    /**
     * Returns current header from a row with key=>value pairs.
     * The values to be combined are given in $keys, the string to combine them is given by $glue
     * 
     * @param   array   $row    Array to take values from
     * @param   array   $keys   Keys of array fields to combine as a header
     * @param   string  $glue   String to put between merged fields
     * @return  string          Combined string
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-07-01
     */
    protected function getCurrentHeader($row, $keys, $glue) {
    	$currentHeader = '';
    	for ($i = 0; $i < sizeof($keys) - 1; $i++) {
    		$currentHeader .= $row[$keys[$i]] . $glue;
    	}
    	$currentHeader .= $row[$keys[$i]];
    	return $currentHeader;
    }
    
    
    
    /**
     * Returns all values from an associative array for a given key
     * 
     * @param   array   $array  Array to take values from
     * @param   string  $key    Key for values that should be taken from $array
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-07-01
     */
    protected function getArrayKeys($array, $key) {
    	$returnArray = array();
    	foreach ($array as $wurscht => $value) {
    		$returnArray[] = $value[$key];
    	}
    	return $returnArray;
    }
    
    
    
    /**
     * Returns a string of combined columns from $row given in $keys
     * 
     * @param   array   $row    Array to take values to combine from
     * @param   array   $keys   Array of keys to combin from $row
     * @return  string          Combined values from row
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-07-01
     */
    protected function combineCols($row, $keys) {
    	$combinedCol = '';
    	foreach ($keys as $key) {
    		$combinedCol .= $row[$key];
    	}
    	return $combinedCol;
    }
    
    
}

?>