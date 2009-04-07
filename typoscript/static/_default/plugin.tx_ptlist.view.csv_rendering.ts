##############################################################
# Configuration for CSV rendering
##############################################################

plugin.tx_ptlist.view.csv_rendering {

	# Set whether file should be generated for I (send to browser), D (send to browser, force download)
	fileHandlingType = I
	
	# Set file name of download file
	fileName = export.csv
	
	# Set to 1 if date and timestamp should be used for filename
	useDateAndTimestampInFilename = 1
	
	# File prefix for default naming (with date and time in filename) (only works, if useDateAndTimestampInFilename is set to 1!)
	fileNamePrefix = csv_
	
}