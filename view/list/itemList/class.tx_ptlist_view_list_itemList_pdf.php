<?php

require_once t3lib_extMgm::extPath('pt_list').'view/class.tx_ptlist_view.php';

class tx_ptlist_view_list_itemList_pdf extends tx_ptlist_view {


	
	public function __construct($controller=NULL) {
		parent::__construct($controller);
		$this->itemsArray['footerReference']='1234';
	}
	
	

	/**
	 * Overwriting the render method to generate a PDF output
	 *
	 * @param	void
	 * @return	void (never returns)
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-02-19
	 */
	public function render() {

		// check if the pt_xml2pdf extension is loaded
		if (!t3lib_extMgm::isLoaded('pt_xml2pdf')) {
			throw new tx_pttools_exception('You need to install the "pt_xml2pdf" extension if you want to export lists as pdfs!');
		}
		require_once t3lib_extMgm::extPath('pt_xml2pdf').'res/class.tx_ptxml2pdf_generator.php';

		// in this case we want special smarty delimiters that are valid xml, so we set some individual smarty configuration
		$this->set_smartyLocalConfiguration(array(
			'left_delimiter' => '<!--{',
			'right_delimiter' => '}-->'
		));

		// remove all TYPO3 output that may be rendered before
		ob_clean();
		
		// load TS configuration for PDF generation
		$pdfFilename = tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.fileName');
		tx_pttools_assert::isNotEmptyString($pdfFilename, array('message' => '$pdfFilename must not be empty but was ' .$pdfFilename));
		$downloadType = tx_pttools_div::getTS('plugin.tx_ptlist.view.pdf_rendering.fileHandlingType');
		tx_pttools_assert::isNotEmptyString($downloadType, array('message' => '$downloadType must not be empty but was ' . $downloadType));

		// generate pdf and output it directly to the browser
		$xmlRenderer = new tx_ptxml2pdf_generator();
		$xmlRenderer->set_xmlSmartyTemplate($this->templateFilePath)
			->set_languageFile('EXT:pt_list/locallang.xml')
            // ->set_languageKey($conf['languageKey'])
            ->addMarkers($this->itemsArr)
			->createXml()
			->renderPdf($pdfFilename, $downloadType);

		// stop execution to avoid some content to be rendered after this output by TYPO3
		exit();
	}
}

?>