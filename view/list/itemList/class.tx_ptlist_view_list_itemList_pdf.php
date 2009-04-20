<?php



/***************************************************************
*  Copyright notice
*
*  (c) 2009 Rainer Kuhn <kuhn@punkt.de>
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



/**Class definition file for PDF Renderer for pt_list listings
 *
 * $Id$
 *
 * @author  Fabrizio Branca, Michael Knoll <knoll@punkt.de>
 * @since   2009-04-20
 */ 



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_list').'view/class.tx_ptlist_view.php';



/**
 * PDF Renderer for rendering pt_list listings in PDF
 * 
 * @author Fabrizio Branca, Michael Knoll <knoll@punkt.de>
 * @since  2009-04-20
 * @package TYPO3
 * @subpackage pt_list
 */
class tx_ptlist_view_list_itemList_pdf extends tx_ptlist_view {



    /**
     * @var string  Filename of PDF file to be generated
     */    
    protected $pdfFilename;
    
    
    
    /**
     * @var string  Download Type of generated PDF file (@see fpdf documentation for details!)
     */
    protected $downloadType;
    
    
    
    /**
     * @var array Holds an array of TS configurations for each column
     */
    protected $columnPdfConfig;
    
    
    
    /**
     * Constructor for pdf rendering view
     * 
     * @param  tx_ptmvc_controlller    $controller        Controller for view
     * @return void
     * @author Fabrizio Branca <branca@punkt.de>
     * @since  2009-04-20
     */ 
    public function __construct($controller=NULL) {
    
        parent::__construct($controller);
        $this->initProperties();
        
    }
    
    

    /**
     * Overwriting the render method to generate a PDF output
     *
     * @param    void
     * @return    void (never returns)
     * @author    Fabrizio Branca <branca@punkt.de>
     * @since    2009-02-19
     */
    public function render() {
    	
    	$this->initTsConfig();
        $this->initColumnPdfConfig();
        
        $this->itemsArr['__config']['columns_total_width_sum'] = $this->getColumnsTotalWidthSum();
        $this->itemsArr['__config']['columns_fixed_width_sum'] = $this->getColumnsFixedWidthSum();  
        $this->itemsArr['__config']['column_widths_scaled'] = $this->getColumnWidthsScaled();
        $this->itemsArr['__config']['column_widths_non_scaled'] = $this->getColumnWidthsNonScaled();
        $this->itemsArr['__config']['column_positions_scaled'] = $this->getColumnPositionsScaled();
        $this->itemsArr['__config']['column_positions_non_scaled'] = $this->getColumnPositionsNonScaled();

        // check if the pt_xml2pdf extension is loaded
        if (!t3lib_extMgm::isLoaded('pt_xml2pdf')) {
            throw new tx_pttools_exception('You need to install the "pt_xml2pdf" extension if you want to export lists as pdfs!');
        }
        require_once t3lib_extMgm::extPath('pt_xml2pdf').'res/class.tx_ptxml2pdf_generator.php';

        $this->setSpecialSmartyDelimiters();

        // remove all TYPO3 output that may be rendered before
        ob_clean();

        // generate pdf and output it directly to the browser
        $xmlRenderer = new tx_ptxml2pdf_generator();
        $xmlRenderer->set_xmlSmartyTemplate($this->templateFilePath)
            ->set_languageFile('EXT:pt_list/locallang.xml')
            // ->set_languageKey($conf['languageKey'])
            ->addMarkers($this->itemsArr)
            ->createXml()
            ->renderPdf($this->pdfFilename, $this->downloadType)
;
        // stop execution to avoid some content to be rendered after this output by TYPO3
       exit();
        
    }
    
    
    
    /**
     * Read TS configuration for PDF generation
     * 
     * @return void
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-04-20
     */
    protected function initTsConfig() {
        
        $this->itemsArr['__config'] = array (
            'page_format'         => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.pageFormat'),
	        'page_height'         => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.pageHeight'),
	        'page_width'          => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.pageWidth'),
            'font_size'           => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.fontSize'),        
            'heading_font_size'   => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.headingFontSize'),
	        'margin_top'          => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.marginTop'),
	        'margin_bottom'       => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.marginBottom'),
	        'margin_right'        => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.marginRight'),
	        'margin_left'         => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.marginLeft'),
            'paper_orientation'   => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.paperOrientation'),
            'increase_col_width'  => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.increaseColWidth'),
            'decrease_col_width'  => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.decreaseColWidth'),
            'list_heading'        => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.listHeading'),
        );
        
        if (TYPO3_DLOG) t3lib_div::devLog('PDF configuration for pt_list', 'pt_list', 0, array('configuration' => $this->itemsArr['__config']));  

    }
    
    
    
    /**
     * Initialization of properties
     * 
     * @return void
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-04-20
     */
    protected function initProperties() {
    
        // load TS configuration for PDF generation
        $this->pdfFilename = tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.fileName');
        tx_pttools_assert::isNotEmptyString($this->pdfFilename, array('message' => '$pdfFilename must not be empty but was ' .$this->pdfFilename));
        if (TYPO3_DLOG) t3lib_div::devLog('Filename for PDF List', 'pt_list', 0, array('filename' => $this->pdfFilename));
        $this->downloadType = tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.fileHandlingType');
        tx_pttools_assert::isNotEmptyString($this->downloadType, array('message' => '$downloadType must not be empty but was ' . $this->downloadType));
        if (TYPO3_DLOG) t3lib_div::devLog('Download type for PDF File', 'pt_list', 0, array('downloadtype' => $this->downloadType));
        
    }
    
    
    
    /**
     * Sets <!--{ and }--> as special delimiters for smarty templates to be XML conform
     * 
     * @return void
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-04-20
     */ 
    protected function setSpecialSmartyDelimiters() {
    
        // in this case we want special smarty delimiters that are valid xml, so we set some individual smarty configuration
        $this->set_smartyLocalConfiguration(array(
            'left_delimiter' => '<!--{',
            'right_delimiter' => '}-->'
        ));
        
    }
    
    
    
    /**
     * Reads column PDF configuration from TS
     * 
     * @return void
     * @author Michael Knoll <knoll@punkt.de>
     * @since 2009-04-20
     */
    protected function initColumnPdfConfig() {
        
        $this->columnPdfConfig = array();
        $tmpConfig = tx_pttools_div::getTS('plugin.tx_ptlist.listConfig.' . $this->itemsArr['listIdentifier'] . '.columns.');
        $tmpArray = array();
        foreach($tmpConfig as $key => $value) {
            $tmpArray[$key] = $value;
        }
        ksort($tmpArray, SORT_NUMERIC);
        foreach ($tmpArray as $pdfColumnConfig) {
        	// Check, whether column should be displayed 
        	if (array_key_exists($pdfColumnConfig['columnIdentifier'], $this->itemsArr['columns'])) {
                $this->columnPdfConfig[$pdfColumnConfig['columnIdentifier']] = $pdfColumnConfig['pdf.'];
        	}
        }
        if (TYPO3_DLOG) t3lib_div::devLog('Column configurations', 'pt_list', 0, array('configuration' => $this->columnPdfConfig));
        
    }
    
    
    
    /***************************************************************************
     * Helper methods for calculations
     ***************************************************************************/
    
    
    protected function getEffPageWidth() {
    	
    	return $this->itemsArr['__config']['page_width'] - $this->itemsArr['__config']['margin_left'] - $this->itemsArr['__config']['margin_right'];
    	
    }
    
    
    
    protected function getEffPageHeight() {
    	
    	return $this->itemsArr['__config']['page_height'] - $this->itemsArr['__config']['margin_top'] - $this->itemsArr['__config']['margin_bottom'];
    	
    }
    
    
    
    protected function getColumnsTotalWidthSum() {
    	
    	$columnsWidthSum = 0;
    	foreach($this->columnPdfConfig as $key => $columnConfArr) {
    		$columnsWidthSum += $columnConfArr['width'];
    	}
    	return $columnsWidthSum;
    	
    }
    
    
    
    protected function getColumnsFixedWidthSum() {
    	
    	$columnsFixedWidthSum = 0;
    	foreach($this->columnPdfConfig as $key => $columnConfArr) {
    		if ($columnConfArr['dontStretch'] == 1) {
    		    $columnsFixedWidthSum += $columnConfArr['width'];
    		}
    	}

    	return $columnsFixedWidthSum;
    	
    }
    
    
    
    protected function getColumnWidthsNonScaled() {
    	
    	$columnWidthsNonScaled = array();
    	foreach($this->columnPdfConfig as $columnIdentifier => $columnConfig) {
    		if ($columnConfig['showColumn'] == 1) {
    		  $columnWidthsNonScaled[] = $columnConfig['width'];
    		}
    	}
    	return $columnWidthsNonScaled;
    	
    }
    
    
    
    protected function getScaleFactor() {
    	
    	return ($this->getEffPageWidth() - $this->getColumnsFixedWidthSum()) / ($this->getColumnsTotalWidthSum() - $this->getColumnsFixedWidthSum());
    	
    }

    
    
    protected function getColumnWidthsScaled() {
    	
    	$scaleFactor = $this->getScaleFactor();
    	$columnWidthsScaled = array();
    	foreach($this->columnPdfConfig as $columnIdentifier => $columnConfig) {
    		if ($columnConfig['dontStretch'] != 1) {
    			$columnWidthsScaled[] = $columnConfig['width'] * $scaleFactor;
    		} else {
    			$columnWidthsScaled[] = $columnConfig['width'];
    		}
    	}
    	return $columnWidthsScaled;
    	
    }
    
    
    protected function getColumnPositionsNonScaled() {
    	
    	$columnPositionsNonScaled = array();
    	$oldPos = $this->itemsArr['__config']['margin_left'];
    	foreach ($this->getColumnWidthsNonScaled() as $columnWidth) {
    		$columnPositionsNonScaled[] = $oldPos;
    		$oldPos += $columnWidth;
    	}
    	return $columnPositionsNonScaled;
    	
    }
    
    
    
    protected function getColumnPositionsScaled() {
    	
    	$columnPositionsScaled = array();
    	$oldPos = $oldPos = $this->itemsArr['__config']['margin_left'];
        foreach ($this->getColumnWidthsScaled() as $columnWidth) {
            $columnPositionsScaled[] = $oldPos;
            $oldPos += $columnWidth;
        }
    	return $columnPositionsScaled;
    	
    }
    
    
}

?>