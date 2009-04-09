<?php



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
	 * @author	Fabrizio Branca <branca@punkt.de>
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
			$row = tx_pttools_div::iconvArray($row, "UTF-8", "ISO-8859-1");
            fputcsv($out, $row, ";");
		}

        fclose($out);

        exit();
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
	 * @author	Michael Knoll <knoll@punkt.de>
	 * @since	2009-04-07
	 */
	protected function sendHeader($filename) {
		
		$downloadType = tx_pttools_div::getTS('plugin.tx_ptlist.view.csv_rendering.fileHandlingType');
		tx_pttools_assert::isNotEmptyString($downloadType, array('message' => '$downloadType must not be empty but was ' . $downloadType));
		
		if ($downloadType == '') {
			$downloadType = 'D';
		}
		
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
                 // TODO add possibility to save on server or return as string!
//                case 'F':
//                        //Save to local file
//                        $f=fopen($name,'wb');
//                        if(!$f)
//                                $this->Error('Unable to create output file: '.$name);
//                        fwrite($f,$this->buffer,strlen($this->buffer));
//                        fclose($f);
//                        break;
//                case 'S':
//                        //Return as a string
//                        return $this->buffer;
                default:
                        throw new tx_pttools_exceptionInternal('No valid download handling set for CSV file!');
        }
		
	}
	
	

}

?>