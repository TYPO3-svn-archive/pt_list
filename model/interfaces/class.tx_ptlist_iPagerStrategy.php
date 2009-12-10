<?php

interface tx_ptlist_iPagerStrategy {
	
	public function setConfiguration(array $configuration);
	
	public function setCurrentPageNumber($pageNumber);
	
	public function setAmountPages($amountPages);
	
	public function getLinks();
	
}

?>