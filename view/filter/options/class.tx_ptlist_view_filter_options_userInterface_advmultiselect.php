<?php

require_once t3lib_extMgm::extPath('pt_mvc').'classes/class.tx_ptmvc_viewSmarty.php';

class tx_ptlist_view_filter_options_userInterface_advmultiselect extends tx_ptmvc_viewSmarty {

	public function getTemplateFilePath() {
		// we do not need any template file here
	}

	public function render() {

		$pre = $this->controller->prefixId;

		$form = new HTML_QuickForm(
			$formName = $this->getViewName() . '-' . $this->controller->get_filterIdentifier(),
			$method = 'post',
			$action = $GLOBALS['TSFE']->cObj->getTypoLink_URL($GLOBALS['TSFE']->id),
			$target = '',
			$attributes = array(
				'class' => 'advmultiselect-form',
			),
			$trackSubmit = false
		);

		$possibleValues = array();
		foreach ($this->getItemById('possibleValues') as $possibleValueArray) {
			$possibleValues[urlencode($possibleValueArray['item'])] = $possibleValueArray['label'];
		}

		$controllerConf = $this->controller->get_conf();

		if (!isset($controllerConf['advmultiselectConf.']['style'])) {
			$controllerConf['advmultiselectConf.']['style'] = '';
		}
		if ($controllerConf['advmultiselectConf.']['size'] == 'all') {
			$controllerConf['advmultiselectConf.']['size'] = count($possibleValues);
		}

		$ams = $form->addElement(
			'advmultiselect',
			sprintf('%s[value]', $pre),
			'',
			$possibleValues,
			$controllerConf['advmultiselectConf.']
		);

		$ams->setLabel(array('', 'Available', 'Selected'));

		$template = '
		<table{class}>
		<!-- BEGIN label_2 --><tr><th align="center">{label_2}</th><!-- END label_2 -->
		<!-- BEGIN label_3 --><th align="center">{label_3}</th></tr><!-- END label_3 -->
		<tr>
		  <td style="width: 50%">{unselected}</td>
		  <td style="width: 50%">{selected}</td>
		</tr>
		<tr>
		  <td align="center">{add}</td>
		  <td align="center">{remove}</td>
		</tr>
		</table>';
		$ams->setElementTemplate($template);

		$GLOBALS['TSFE']->additionalHeaderData[$this->getViewName().'_ams'] = $ams->getElementJs(false);

		$form->setDefaults(array($pre => array('value' => $this->getItemById('value'))));

		$form->addElement('hidden', $pre.'[action]', 'submit');
		$form->addElement('submit', $pre.'[submit]', 'Daten abschicken');

		return $form->toHtml();
	}
}

?>