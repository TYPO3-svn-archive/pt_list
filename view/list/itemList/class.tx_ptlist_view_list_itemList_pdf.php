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
 * Class definition file for PDF Renderer for pt_list listings
 *
 * $Id$
 *
 * @author  Fabrizio Branca, Michael Knoll <knoll@punkt.de>
 * @since   2009-04-20
 */ 



/**
 * Inclusion of external ressources
 */
require_once t3lib_extMgm::extPath('pt_list') . 'view/class.tx_ptlist_view.php';
require_once t3lib_extMgm::extPath('pt_tools') . 'res/staticlib/class.tx_pttools_div.php';



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
     * @var string  Holds the encoding of the Database
     */
    protected $dbEncoding;
    
    
    
    /**
     * Constructor for pdf rendering view
     * 
     * @param  tx_ptmvc_controlller    $controller        Controller for view
     * @return void
     * @author Fabrizio Branca <mail@fabrizio-branca.de>
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
     * @author    Fabrizio Branca <mail@fabrizio-branca.de>
     * @since    2009-02-19
     */
    public function render() {
    	
    	$this->initTsConfig();
        $this->initColumnPdfConfig();
        $this->setUpConfigArray();
        $this->setSpecialSmartyDelimiters();
        $this->encodeItemsArrToUtf8();
        $this->beforeRendering();

        // check if the pt_xml2pdf extension is loaded
        if (!t3lib_extMgm::isLoaded('pt_xml2pdf')) {
            throw new tx_pttools_exception('You need to install the "pt_xml2pdf" extension if you want to export lists as pdfs!');
        }
        require_once t3lib_extMgm::extPath('pt_xml2pdf').'res/class.tx_ptxml2pdf_generator.php';

        // remove all TYPO3 output that may be rendered before
        ob_clean();

        // generate pdf and output it directly to the browser
        $xmlRenderer = new tx_ptxml2pdf_generator();
        $xmlRenderer->set_xmlSmartyTemplate($this->templateFilePath)
            ->set_languageFile('EXT:pt_list/locallang.xml')
            // ->set_languageKey($conf['languageKey'])
            ->addMarkers($this->itemsArr)
            ->createXml()
            ->renderPdf($this->pdfFilename, $this->downloadType);
       // stop execution to avoid some content to be rendered after this output by TYPO3
       exit();
        
    }
    
        
    
    /**
     * Overwriting the addItem method to make html filtering non-default for PDF contents
     * 
     * Settings for filterHtml can be overwritten by TS!
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
    
    
    
    /***************************************************************************
     * Helper methods for initialization
     ***************************************************************************/
    
    
    
    /**
     * Convert itemsArray to utf-8
     * 
     * @return void
     * @author Michael Knoll <knoll@punkt.de>
     * @since  2009-04-28
     */
    protected function encodeItemsArrToUtf8() {
    	
    	$this->itemsArr = tx_pttools_div::iconvArray($this->itemsArr, $this->dbEncoding, 'UTF-8');
    	
    }
    
    
    
    /**
     * Adds a new configuration item to config array passed to template by a given key
     * 
     * @param   $key    Key of configuration value
     * @param   $value  Value of configuration item
     * @return  void
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-04-21
     */
    protected function addToConfigArray($key, $value) {
    	
    	$this->itemsArr['__config'][$key] = $value;
    	
    }
    
    
    
    /**
     * Set up configration values in marker array
     * 
     * @return  void
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-04-21
     */
    protected function setUpConfigArray() {
    	
    	$this->addToConfigArray('columns_total_width_sum', $this->getColumnsTotalWidthSum());
        $this->addToConfigArray('columns_fixed_width_sum', $this->getColumnsFixedWidthSum());  
        $this->addToConfigArray('column_widths_scaled', $this->getColumnWidthsScaled());
        $this->addToConfigArray('column_widths_non_scaled', $this->getColumnWidthsNonScaled());
        $this->addToConfigArray('column_positions_scaled', $this->getColumnPositionsScaled());
        $this->addToConfigArray('column_positions_non_scaled', $this->getColumnPositionsNonScaled());
        $this->addToConfigArray('column_alignments', $this->getColumnAlignments());
        $this->addToConfigArray('column_multiline', $this->getColumnMultilines());
        
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
            'page_format'               => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.pageFormat'),
            'page_height'               => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.pageHeight'),
            'page_width'                => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.pageWidth'),
            'font_size'                 => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.fontSize'),        
            'heading_font_size'         => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.headingFontSize'),
            'margin_top'                => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.marginTop'),
            'margin_bottom'             => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.marginBottom'),
            'margin_right'              => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.marginRight'),
            'margin_left'               => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.marginLeft'),
            'paper_orientation'         => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.paperOrientation'),
            'increase_col_width'        => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.increaseColWidth'),
            'decrease_col_width'        => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.decreaseColWidth'),
            'list_heading'              => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.listHeading'),
            'list_heading_font_size'    => tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.listHeadingFontSize'),
        );
        
        // This can only be assigned AFTER the upper array is initialized!
        $this->itemsArr['__config']['effective_width'] = $this->getEffPageWidth();
        
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
        $this->downloadType = tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.fileHandlingType') != '' ? 
            tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.fileHandlingType') : 'I';
        tx_pttools_assert::isNotEmptyString($this->downloadType, array('message' => '$downloadType must not be empty but was ' . $this->downloadType));
        if (TYPO3_DLOG) t3lib_div::devLog('Download type for PDF File', 'pt_list', 0, array('downloadtype' => $this->downloadType));
        $this->dbEncoding = tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.dbEncoding');
        if (TYPO3_DLOG) t3lib_div::devLog('DB Encoding for pt_list pdf rendering', 'pt_list', 0, array('dbEncoding' => $this->dbEncoding));
        
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
     * Helper methods for calculations and formatting of pdf
     ***************************************************************************/
    
    
    /**
     * Returns effectiv page width (page width minus borders)
     * 
     * @return  float   Effective page width
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-04-21
     */
    protected function getEffPageWidth() {
    	
    	return $this->itemsArr['__config']['page_width'] - $this->itemsArr['__config']['margin_left'] - $this->itemsArr['__config']['margin_right'];
    	
    }
    
    
    
    /**
     * Returns effective page height (page height minus borders)
     * 
     * @return  float   Effective page height
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-04-21
     */
    protected function getEffPageHeight() {
    	
    	return $this->itemsArr['__config']['page_height'] - $this->itemsArr['__config']['margin_top'] - $this->itemsArr['__config']['margin_bottom'];
    	
    }
    
    
    
    /**
     * Returns sum of column widths
     * 
     * @return  float   Sum of column widths
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-04-21
     */
    protected function getColumnsTotalWidthSum() {
    	
    	$columnsWidthSum = 0;
    	foreach($this->columnPdfConfig as $key => $columnConfArr) {
    		$columnsWidthSum += $columnConfArr['width'];
    	}
    	return $columnsWidthSum;
    	
    }
    
    
    
    /**
     * Returns sum of column widths for columns with fixed widths
     * 
     * @return  float   Sum of column widths for columns with fixed widths
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-04-21
     */
    protected function getColumnsFixedWidthSum() {
    	
    	$columnsFixedWidthSum = 0;
    	foreach($this->columnPdfConfig as $key => $columnConfArr) {
    		if ($columnConfArr['dontScale'] == 1) {
    		    $columnsFixedWidthSum += $columnConfArr['width'];
    		}
    	}

    	return $columnsFixedWidthSum;
    	
    }
    
    
    
    /**
     * Returns array of widths of non-scaled columns
     * 
     * @return  array   Widths of non-scaled columns
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-04-21
     */
    protected function getColumnWidthsNonScaled() {
    	
    	$columnWidthsNonScaled = array();
    	foreach($this->columnPdfConfig as $columnIdentifier => $columnConfig) {
    		if ($columnConfig['showColumn'] == 1) {
    		  $columnWidthsNonScaled[] = $columnConfig['width'];
    		}
    	}
    	return $columnWidthsNonScaled;
    	
    }
    
    
    
    /**
     * Returns scaling factor for scaling column widths to fit on page
     * 
     * @return  float   Scaling factor
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-04-21
     */
    protected function getScaleFactor() {
    	
    	return ($this->getEffPageWidth() - $this->getColumnsFixedWidthSum()) / ($this->getColumnsTotalWidthSum() - $this->getColumnsFixedWidthSum());
    	
    }

    
    
    /**
     * Returns array of widths for scaled columns
     * 
     * @return  array   Widths of scaled columns
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-04-21
     */
    protected function getColumnWidthsScaled() {
    	
    	$scaleFactor = $this->getScaleFactor();
    	$columnWidthsScaled = array();
    	foreach($this->columnPdfConfig as $columnIdentifier => $columnConfig) {
    		if ($columnConfig['dontScale'] != 1) {
    			$columnWidthsScaled[] = $columnConfig['width'] * $scaleFactor;
    		} else {
    			$columnWidthsScaled[] = $columnConfig['width'];
    		}
    	}
    	return $columnWidthsScaled;
    	
    }
    
    
    
    /**
     * Returns array of column positions for non-scaled column widths
     * 
     * @return  array   Positions of non-scaled columns
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-04-21
     */
    protected function getColumnPositionsNonScaled() {
    	
    	$columnPositionsNonScaled = array();
    	$oldPos = $this->itemsArr['__config']['margin_left'];
    	foreach ($this->getColumnWidthsNonScaled() as $columnWidth) {
    		$columnPositionsNonScaled[] = $oldPos;
    		$oldPos += $columnWidth;
    	}
    	return $columnPositionsNonScaled;
    	
    }
    
    
    
    /**
     * Returns array of column positions for scaled column widths
     * 
     * @return  array   Positions of scaled columns
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-04-21
     */
    protected function getColumnPositionsScaled() {
    	
    	$columnPositionsScaled = array();
    	$oldPos = $oldPos = $this->itemsArr['__config']['margin_left'];
        foreach ($this->getColumnWidthsScaled() as $columnWidth) {
            $columnPositionsScaled[] = $oldPos;
            $oldPos += $columnWidth;
        }
    	return $columnPositionsScaled;
    	
    }
    
    
    
    /**
     * Returns an array of alignments for columns
     * 
     * @return  array   Alignments for columns
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-04-21
     */
    protected function getColumnAlignments() {
    	
    	$columnAlignments = array();
    	foreach ($this->columnPdfConfig as $columnIdentifier => $columnConfig) {
    		$columnAlignments[] = $columnConfig['alignment'];
    	}
    	return $columnAlignments;
    	
    }
    
    
    
    /**
     * Returns an array of multiline settings for columns
     * (which column should be rendered using multilines?)
     * 
     * @return  array   Multiline settings of columns
     * @author  Michael Knoll <knoll@punkt.de>
     * @since   2009-04-23
     */
    protected function getColumnMultilines() {
    	
    	$columnMultilines = array();
    	foreach ($this->columnPdfConfig as $columnIdentifier => $columnConfig) {
    		$columnMultilines[] = $columnConfig['multiline'];
    	}
    	return $columnMultilines;
    	
    }
    
    
}

?>