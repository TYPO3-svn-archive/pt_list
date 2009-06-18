<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Fabrizio Branca, Michael Knoll <knoll@punkt.de>
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
 * Class definition file for CSV Renderer for pt_list listings
 *
 * $Id$
 *
 * @author  Fabrizio Branca, Michael Knoll <knoll@punkt.de>
 * @since   2009-02-19
 */ 


require_once t3lib_extMgm::extPath('pt_list').'view/class.tx_ptlist_view.php';



class tx_ptlist_view_list_itemList_csv extends tx_ptlist_view {

	
	
	protected $typoScriptMode = false;

	

	public function getTemplateFilePath() {
		// we don't need any template here
	}

	
	
	/**
	 * Overwriting the render method to generate a CSV output
	 *
	 * @param	void
	 * @return	void (never returns)
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-19
	 */
	public function render() {
		ob_clean();

		$csvContent = '';
		
		$full_filename = $this->generateFilenameFromTs();

		$this->sendHeader($full_filename);

        $out = fopen('php://output', 'w');

		// Fields
		$header = array();
		foreach ($this->getItemById('columns') as $column) {
				$header[] = $column['label'];
		}
        fputcsv($out, $header, ";");

		// Rows
		foreach ($this->getItemById('listItems') as $row) {
			$row = tx_pttools_div::iconvArray($row, 'UTF-8', 'ISO-8859-1');     // TODO: make encoding configurable via TS
            fputcsv($out, $row, ";");
		}

        fclose($out);

        exit();
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
	 * @return  string		File name of CSV File
	 * @author	Michael Knoll <knoll@punkt.de>
	 * @since	2009-04-07
	 */
	protected function generateFilenameFromTs() {
		
		// load TS configuration for CSV generation
		$csvfFilename = tx_pttools_div::getTS('plugin.tx_ptlist.view.csv_rendering.fileName');
		$useDateAndTimeInFilename = tx_pttools_div::getTS('plugin.tx_ptlist.view.csv_rendering.useDateAndTimestampInFilename');

		if ($useDateAndTimeInFilename == '1') {
			$fileNamePrefix = tx_pttools_div::getTS('plugin.tx_ptlist.view.csv_rendering.fileNamePrefix');
			if ($fileNamePrefix == '' ) {
				$fileNamePrefix = 'itemlist_';
			}
			$full_filename = $fileNamePrefix.date('Y-m-d', time()) .'.csv';
		} elseif ($csvfFilename != '') {
			$full_filename = $csvfFilename;
		}
		return $full_filename;
		
	}
	
	
	
	/**
	 * Generate header depending on download handling setting in TS
	 * 
	 * Functionality is taken from FPDF!
	 * 
	 * @param   string  name of the file to send to the browser
	 * @return  void
	 * @author	Michael Knoll <knoll@punkt.de>
	 * @since	2009-04-07
	 */
	protected function sendHeader($filename) {
		
		$downloadType = tx_pttools_div::getTS('plugin.tx_ptlist.view.csv_rendering.fileHandlingType');
		
		if ($downloadType == '') {
			$downloadType = 'I';
		}
        tx_pttools_assert::isInList($downloadType, 'D,I', array('message' => 'Invalid download type'));
		
		switch($downloadType)
        {
                case 'I':
                        //We send to a browser
                        header('Content-Type: text/x-csv');
                        if(headers_sent())
                                $this->Error('Some data has already been output to browser, can\'t send CSV file');
                        header('Content-disposition: inline; filename="'.$filename.'"');
                        break;

                case 'D':
                        //Download file
                        if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
                                header('Content-Type: application/force-download');
                        else
                                header('Content-Type: application/octet-stream');
                        header('Content-disposition: attachment; filename="'.$filename.'"');
                        break;

                default:
                        throw new tx_pttools_exceptionInternal('No valid download handling set for CSV file!');
        }
		
	}
	
	

}

?>