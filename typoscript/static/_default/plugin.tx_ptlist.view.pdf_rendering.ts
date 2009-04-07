##############################################################
# Configuration for PDF rendering
##############################################################

plugin.tx_ptlist.view.pdf_rendering {

	# Set whether file should be generated for I (send to browser), D (send to browser, force download), F (local file), S (return as string)
	fileHandlingType = I
	# Set file name of download file
	fileName = listitems.pdf
	
}