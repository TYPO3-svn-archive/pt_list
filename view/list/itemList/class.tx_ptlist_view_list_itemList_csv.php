<?php

require_once t3lib_extMgm::extPath('pt_list').'view/class.tx_ptlist_view.php';

class tx_ptlist_view_list_itemList_csv extends tx_ptlist_view {

	protected $typoScriptMode = false;

	

	public function getTemplateFilePath() {
		// we don't need any template here
	}

	public function render() {
		ob_clean();

		$csvContent = '';

		$full_filename = 'itemlist_'.date('Y-m-d', time()) .'.csv';

		// header("Content-Type: application/octet-stream");
		header("Content-Type: text/x-csv");
        header("Content-Disposition: attachment; filename=\"$full_filename\"");

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

}

?>