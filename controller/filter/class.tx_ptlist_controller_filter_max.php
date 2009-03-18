<?php

require_once t3lib_extMgm::extPath('pt_list').'model/class.tx_ptlist_filter.php';
require_once t3lib_extMgm::extPath('pt_list').'view/filter/max/class.tx_ptlist_view_filter_max_userInterface.php';

class tx_ptlist_controller_filter_max extends tx_ptlist_filter {
	
	
	public function defaultAction() {
		$view = $this->getView('filter_max_userInterface');
		$view->addItem($this->value, 'value');
		return $view->render();
	}
	
	public function submitAction() {
		$this->isActive = true;
		$this->value = $this->params['value'];
		return $this->doAction('default');
	}
	
	public function getSqlWhereClauseSnippet() {
		tx_pttools_assert::isEqual(count($this->dataDescriptions), 1, array('message' => 'This filter can only be used with 1 column'));
        $sqlWhereClauseSnippet = sprintf('%s.%s <= %s', $this->dataDescriptions->getItemByIndex(0)->get_table(), $this->dataDescriptions->getItemByIndex(0)->get_field(), intval($this->value));
        return $sqlWhereClauseSnippet;
	}

	public function setValue( $val ) {
		$this->value = $val;
	}
	
}


?>