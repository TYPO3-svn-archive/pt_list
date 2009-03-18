<?php

require_once t3lib_extMgm::extPath('pt_list').'view/class.tx_ptlist_view.php';

require_once 'Image/Graph.php';

class tx_ptlist_view_list_itemList_imageGraph extends tx_ptlist_view {

	protected $typoScriptMode = false;

	public function getTemplateFilePath() {
	}


	public function render() {

		ob_clean();

		// create the graph
		$Graph =& Image_Graph::factory('graph', array(400, 300));

		// add a TrueType font
		// $Font =& $Graph->addNew('font', 'Verdana');

		// $Font =& $Graph->addNew('ttf_font', 'Gothic');

		// set the font size to 11 pixels
		// $Font->setSize(8);

		// $Graph->setFont($Font);



		// setup the plotarea, legend and their layout

		/*
		$Graph->add(
		  Image_Graph::vertical(
			 $Plotarea = Image_Graph::factory('plotarea'),
			 $Legend = Image_Graph::factory('legend'),
			 70
		  )
		);


		$Legend->setPlotArea($Plotarea);
	    */


		$Graph->add($Plotarea = Image_Graph::factory('plotarea'));

		$Plotarea->hideAxis();


		$plotData = array();

		foreach ($this->getItemById('listItems') as $row) {
			$plotData[$row['projektColumn']] += $row['usedtimRawColumn'];
		}

		ksort($plotData);

		if (TYPO3_DLOG) t3lib_div::devLog('Plotdata', 'pt_list', 1, $plotData);

		// create the dataset
		$Dataset =& Image_Graph::factory('dataset', array($plotData));

		// create the 1st plot as smoothed area chart using the 1st dataset
		$Plot =& $Plotarea->addNew('Image_Graph_Plot_Pie', $Dataset);

		// $Plot->setRestGroup(11, 'Other animals');

		$Plot->Radius = 2;

		// set a line color
		$Plot->setLineColor('gray');

		// set a standard fill style
		$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
		$Plot->setFillStyle($FillArray);

		$FillArray->addColor('green@0.2');
		$FillArray->addColor('blue@0.2');
		$FillArray->addColor('yellow@0.2');
		$FillArray->addColor('red@0.2');
		$FillArray->addColor('orange@0.2');
		$FillArray->addColor('black@0.2', 'rest');
		
		$Plot->explode(10);
		
		// create a Y data value marker
		$Marker =& $Plot->addNew('Image_Graph_Marker_Value', IMAGE_GRAPH_PCT_Y_TOTAL);

		// fill it with white
		$Marker->setFillColor('white');
		// and use black border
		$Marker->setBorderColor('black');
		// and format it using a data preprocessor
		$Marker->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%0.1f%%'));
		$Marker->setFontSize(7);

		// create a pin-point marker type
		$PointingMarker =& $Plot->addNew('Image_Graph_Marker_Pointing_Angular', array(20, &$Marker));
		// and use the marker on the plot
		$Plot->setMarker($PointingMarker);

		// output the Graph
		$Graph->done();

		exit();
	}

}

?>