<?php

require_once t3lib_extMgm::extPath('pt_list').'view/class.tx_ptlist_view.php';

class tx_ptlist_view_list_itemList extends tx_ptlist_view {

	/**
	 * @var bool	in typoscript mode no smarty template will be used
	 */
	protected $typoScriptMode = false;



	/**
	 * Overwrite the getTemplateFilePath to avoid searching for a template if in typoscript mode
	 *
	 * @param	void
	 * @return	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-19
	 */
	public function getTemplateFilePath() {
		if ($this->viewConf['template'] != 'none') {
			$this->typoScriptMode = false;
			parent::getTemplateFilePath();
		} else {
			$this->typoScriptMode = true;
		}
	}



	/**
	 * Overwrite the render method to make a tryposcript mode possible
	 * In typoscript mode no smarty template will be used.
	 * You decide how the list, rows and single fields will be wrapped by typoscript
	 *
	 * @param	void
	 * @return	string	HTML output
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-02-19
	 */
	public function render() {

	    // Set special text for "no items found" if no items in current list
	    // TODO ry21: Replace this by a translation mechanism in pt_mvc
        if (count($this->itemsArr['listItems']) == 0) {
            $this->addItem($this->_extConf['listConfig.'][$this->controller->get_currentlistId() . '.']['noElementsFoundText'], 'no_elements_found_text');
        }
		
		if ($this->typoScriptMode == false) {

			/**
			 * Smarty rendering
			 */
			$output = parent::render();

		} else {

			/**
			 * Typoscript rendering
			 */

			$renderConfig = $this->viewConf['template.'];

			$list = '';

			// iterate over all rows
			foreach ($this->itemsArr['listItems'] as $item) {

				$row = '';

				// iterate over all columns
				foreach ($item as $columnIdentifier => $content) {

					$GLOBALS['TSFE']->cObj->data = array(
						'columnIdentifier' => $columnIdentifier,
						'columnContent' => $content
					);

					$field = $content;
					// if there is a columnIdentifier-specific stdWrap use this, else use "field_stdWrap"
					if (is_array($renderConfig[$columnIdentifier.'_stdWrap.'])) {
						$field = $GLOBALS['TSFE']->cObj->stdWrap($field, $renderConfig[$columnIdentifier.'_stdWrap.']);
					} else {
						$field = $GLOBALS['TSFE']->cObj->stdWrap($field, $renderConfig['field_stdWrap.']);
					}

					$row .= $field;
				}

				$row = $GLOBALS['TSFE']->cObj->stdWrap($row, $renderConfig['row_stdWrap.']);

				$list .= $row;

			}
			$output = $GLOBALS['TSFE']->cObj->stdWrap($list, $renderConfig['list_stdWrap.']);
		}

		return $output;
	}



	/**
	 * This will be executed before rendering the template
	 *
	 * @param 	void
	 * @return 	void
	 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
	 * @since	2009-01-19
	 */
	public function beforeRendering() {
		parent::beforeRendering();
		$this->addItem('&###LISTPREFIX###[action]=changeSortingOrder&###LISTPREFIX###[column]=%s&###LISTPREFIX###[direction]=%s', 'additionalParamsForColumnHeaders');
		
		/* Add empty arrays for non-structured lists (template uses "in_array()") */
		$this->addItem(array(), 'structure_by_cols', false);
	}

}

?>