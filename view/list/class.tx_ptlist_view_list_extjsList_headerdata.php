<?php

require_once t3lib_extMgm::extPath('pt_list').'view/class.tx_ptlist_view.php';

class tx_ptlist_view_list_extjsList_headerdata extends tx_ptlist_view {
	
	protected $smartyLocalConfiguration = array(
		'left_delimiter' => '{{',
		'right_delimiter' => '}}'
	);
	
}

?>