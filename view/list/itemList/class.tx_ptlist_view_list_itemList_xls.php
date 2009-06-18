<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Michael Knoll <knoll@punkt.de>
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
 * Class definition file for XLS Export from pt_list
 *
 * @version $Id:$
 * @author  Michael Knoll <knoll@punkt.de>
 */



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_list').'view/class.tx_ptlist_view.php';
require_once 'Spreadsheet/Excel/Writer.php';        // Remind installing the PEAR package if you want to use XLS export, see http://pear.php.net/package/Spreadsheet_Excel_Writer/download!


/**
 * Pt list view for exporting list contents to XLS format
 *
 * @package TYPO3
 * @subpackage pt_list
 * @author  Michael Knoll
 * @since 2009-06-16
 */
class tx_ptlist_view_list_itemList_xls extends tx_ptlist_view {

	
	
	protected $typoScriptMode = false;
	
	
	/**
	 * Reference to PEAR XLS writer
	 * @var Spreadsheet_Excel_Writer
	 */
	protected $xls;
	
	
	/**
	 * Filename of XLS output file
	 * @var string
	 */
	protected $full_filename;

	

	/**
	 * Template method for determining template path
	 */
	public function getTemplateFilePath() {
		// we don't need any template here
	}

	
	
	/**
	 * Overwriting the render method to generate a XLS output
	 *
	 * @param	void
	 * @return	void (never returns)
	 * @author	Michael Knoll <knoll@punkt.de>
	 * @since	2009-06-16
	 */
	public function render() {
		
		ob_clean();
		$this->full_filename = $this->generateFilenameFromTs();
		$this->generateXlsFile();
        exit();
        
	}
	
	
	
	/**
	 * Generates XLS file from list items
	 * 
	 * @param void
	 * @return void
	 * @author Michael Knoll <knoll@punkt.de>
	 * @since 2009-06-16
	 */
	protected function generateXlsFile() {
		
		$this->xls = new Spreadsheet_Excel_Writer();
		$this->xls->send($this->full_filename);
		$sheetName = tx_pttools_div::getTS('plugin.tx_ptlist.view.xls_rendering.sheetName');
		$sheet =& $this->xls->addWorksheet($sheetName);
        
		$row = 0;
        $col = 0;
		
        // Write Headings for spreadsheet columns
        foreach ($this->getItemById('columns') as $column) {
                $sheet->write($row, $col, $column['label']); 
                $col++;
        }
        $col = 0;
        $row++;
        
        // Write spreadsheet rows
        foreach ($this->getItemById('listItems') as $cells) {
            $row = tx_pttools_div::iconvArray($row, 'UTF-8', 'ISO-8859-1');     // TODO: make encoding configurable via TS
            foreach ($cells as $cell) {
            	$sheet->write($row, $col, $cell);
            	$col++;
            }
            $col = 0;
            $row++;
        }

        $this->xls->close();
		
	}
	
	
	
	/**
     * Overwriting the addItem method to make html filtering non-default for CSV contents
     * 
     * Settings of filterHtml are overwritten by TS!
     * 
     * @param   mixed   $itemObj    Object to add to the view
     * @param   mixed   $id         ID of object to be added to the view
     * @param   bool    $filterHtml Should contents of added object be html filtered?
     * @return  void
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-05-18
     */
    public function addItem($itemObj, $id = 0, $filterHtml = false) {
        
    	/* Call parent method with new filterHtml settings */
        parent::addItem($itemObj, $id, $filterHtml);
        
    }
    
    
	
	/**
	 * Helper method to generate file name from TS config
	 * 
	 * @return  string		File name of XLS File
	 * @author	Michael Knoll <knoll@punkt.de>
	 * @since	2009-04-07
	 */
	protected function generateFilenameFromTs() {
		
		// load TS configuration for CSV generation
		$xlsfFilename = tx_pttools_div::getTS('plugin.tx_ptlist.view.xls_rendering.fileName');
		$useDateAndTimeInFilename = tx_pttools_div::getTS('plugin.tx_ptlist.view.xls_rendering.useDateAndTimestampInFilename');

		if ($useDateAndTimeInFilename == '1') {
			$fileNamePrefix = tx_pttools_div::getTS('plugin.tx_ptlist.view.xls_rendering.fileNamePrefix');
			if ($fileNamePrefix == '' ) {
				$fileNamePrefix = 'itemlist_';
			}
			$full_filename = $fileNamePrefix.date('Y-m-d', time()) .'.xls';
		} elseif ($xlsfFilename != '') {
			$full_filename = $xlsfFilename;
		}
		return $full_filename;
		
	}

}

?>