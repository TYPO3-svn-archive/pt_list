<?php

/**
 * TYPO3 tables data renderer
 *
 * @version 	$Id: class.tx_ptlist_typo3Tables_renderer.php,v 1.2 2009/02/10 13:55:13 ry44 Exp $
 * @author		Fabrizio Branca <branca@punkt.de>
 * @since		2009-01-30
 */
class tx_ptlist_typo3Tables_renderer {
	
	/**
	 * This renderer appends an image with the current country flag (if available) to the current content
	 *
	 * @param 	array 	array('currentContent' => <currentContent>, 'values' => array('<dataDescriptionIdentifier>' => '<value>'))
	 * @return 	string 	rendered content
	 * @author	Fabrizio Branca <branca@punkt.de>
	 * @since	2009-02-10
	 */
	public static function iso2CodeRenderer(array $params) {
		
		$values = $params['values'];
		$currentContent = $params['currentContent'];
		
		if (isset($values['iso2'])) {
			// check if file exists
			$flagFileName =  'gfx/flags/' . strtolower($values['iso2']) . '.gif';
			if (is_file(PATH_typo3 . $flagFileName)) {
				$currentContent .= ' <img src="/typo3/'. $flagFileName .'" />'; 
			}
		}
		return $currentContent;
	}
	
	public static function nameAndCapitalRenderer(array $params) {
		$values = $params['values'];
		return sprintf('%s (%s)', $values['name_local'], $values['capital']);
	}
	

}

?>