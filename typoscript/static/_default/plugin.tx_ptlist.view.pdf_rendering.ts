################################################################################
# pt_list Setup for PDF Rendering
#
# Put this template on the page where your PDF rendering should take place.
# There should be a template in a parent page setting up all the rest of
# the pt_list configurations (columns etc.)! Feel free to override
# the columns for PDF rendering on an extension template on the site,
# where the PDF rendering takes place.
# 
# @version  $Id:$
# @author   Michael Knoll <knoll@punkt.de>, Fabrizio Branca <mail@fabrizio-branca.de>
# @since    2009-04-21
################################################################################


################################################################################
# we create a minimal page object the uses only the content 
# of the middle column (no menues etc.)
################################################################################

page >
page = PAGE
page.10 < styles.content.get



###########################################################################
# MVC config: change view
###########################################################################

# Overwrite view for PDF generation
plugin.tx_ptlist.view.list_itemList {
    class = EXT:pt_list/view/list/itemList/class.tx_ptlist_view_list_itemList_pdf.php:tx_ptlist_view_list_itemList_pdf
}

# Set template for PDF view
plugin.tx_pteublis.view.list_itemList_pdf {
    template = EXT:pt_list/template/list/itemList/list_itemList_pdf.tpl
}



###########################################################################
# general pt_list config
###########################################################################

# Set PDF file properties
plugin.tx_ptlist.view.pdf_rendering {

    # If fileHandlingType is set to F you can define additional directories for saving file to (e.g. fileadmin/myFileName.pdf)
    fileName = file_name_of_pdf.pdf
    
    # Additional file handling config possible here (e.g. fileHandlingType uses FDPF output parameters):
    # set whether file should be generated for I (send to browser), D (send to browser, force download), F (save file to server), S (return as string)
    fileHandlingType = I
    
    ####################################################################### 
    # PDF page configuration
    ####################################################################### 
    
    # DIN page format
    pageFormat = A4
    # Height of page
    pageHeight = 210
    # Width of page
    pageWidth = 297
    # Font size for table headings
    headingFontSize = 8
    # Font size for cells
    fontSize = 7       
    # Page margin on the top
    marginTop = 15
    # Page margin on the bottom
    marginBottom = 20
    # Page margin on the right
    marginRight = 15 
    # Page margin on the left
    marginLeft = 15
    # Orientation of page (L)andscape or (P)ortrait
    paperOrientation = L
    # Increase width of col to cover whole visible page area
    increaseColWidth = 1
    # Decrease col width if it overflows visible page area
    decreaseColWidth = 1
    # Heading for list
    listHeading = Lieferantenverzeichnis
    # Font size for list header
    listHeadingFontSize = 14
    
}