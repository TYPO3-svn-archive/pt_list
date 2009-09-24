################################################################################
## Simple alias list
################################################################################

plugin.tx_ptlist.alias {
    
    # filter_*: filter classes inheriting from the tx_ptlist_filter class
	filter_string = EXT:pt_list/controller/filter/class.tx_ptlist_controller_filter_string.php:tx_ptlist_controller_filter_string
	filter_min = EXT:pt_list/controller/filter/class.tx_ptlist_controller_filter_min.php:tx_ptlist_controller_filter_min
	filter_max = EXT:pt_list/controller/filter/class.tx_ptlist_controller_filter_max.php:tx_ptlist_controller_filter_max
    filter_range = EXT:pt_list/controller/filter/class.tx_ptlist_controller_filter_range.php:tx_ptlist_controller_filter_range
    filter_timeSpan = EXT:pt_list/controller/filter/class.tx_ptlist_controller_filter_timeSpan.php:tx_ptlist_controller_filter_timeSpan
    filter_timeSpan2 = EXT:pt_list/controller/filter/class.tx_ptlist_controller_filter_timeSpan2.php:tx_ptlist_controller_filter_timeSpan2
    filter_firstLetter = EXT:pt_list/controller/filter/class.tx_ptlist_controller_filter_firstLetter.php:tx_ptlist_controller_filter_firstLetter
    filter_datePicker = EXT:pt_list/controller/filter/class.tx_ptlist_controller_filter_datePicker.php:tx_ptlist_controller_filter_datePicker
    filter_datePager = EXT:pt_list/controller/filter/class.tx_ptlist_controller_filter_datePager.php:tx_ptlist_controller_filter_datePager

    filter_options_group = EXT:pt_list/controller/filter/options/class.tx_ptlist_controller_filter_options_group.php:tx_ptlist_controller_filter_options_group
    filter_options_explicit = EXT:pt_list/controller/filter/options/class.tx_ptlist_controller_filter_options_explicit.php:tx_ptlist_controller_filter_options_explicit
    

    # pagerStrategy_*: pager strategy classes implementing the tx_ptlist_iPagerStrategy interface
	pagerStrategy_default = EXT:pt_list/model/pagerStrategy/class.tx_ptlist_pagerStrategy_default.php:tx_ptlist_pagerStrategy_default

    # renderer_*: renderer user functions
	renderer_cObject = EXT:pt_list/model/class.tx_ptlist_renderer.php:tx_ptlist_renderer->cObject
	renderer_editIcon = EXT:pt_list/model/class.tx_ptlist_renderer.php:tx_ptlist_renderer->editIcon
	renderer_regexReplace = EXT:pt_list/model/class.tx_ptlist_renderer.php:tx_ptlist_renderer->regexReplace
	renderer_fetchExternalData = EXT:pt_list/model/class.tx_ptlist_renderer.php:tx_ptlist_renderer->fetchExternalData

    # list_*: list classes 
	list_typo3Tables = EXT:pt_list/model/typo3Tables/class.tx_ptlist_typo3Tables_list.php:tx_ptlist_typo3Tables_list
    
    # userFunction_*: user functions for filters
    userFunction_redirectOnValidate = EXT:pt_list/model/class.tx_ptlist_div.php:tx_ptlist_div->redirectOnValidate
}
