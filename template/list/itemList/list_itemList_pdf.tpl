<?xml version="1.0" encoding="UTF-8"?>
<document orientation="<!--{$__config.paper_orientation}-->" unit="mm" format="<!--{$__config.page_format}-->"><!--{strip}-->
    <!-- Main configuration -->
    <!--{assign var="borderLeft" value=$__config.margin_left}-->
    <!--{assign var="borderRight" value=$__config.margin_right}-->
    <!--{assign var="borderTop" value=$__config.margin_top}-->
    <!--{assign var="borderBottom" value=$__config.margin_bottom}-->
    <!--{assign var="paperWidth" value=$__config.page_width}-->
    <!--{assign var="paperHeight" value=$__config.page_height}-->
    
    <header>
        <allpages>
            <!-- ########################### GENERATE HEADING ########################################### -->
            <setfont family="Arial" style="" size="<!--{$__config.list_heading_font_size}-->" />
            <sety y="<!--{$borderTop}-->" />
            <setx x="<!--{$borderLeft}-->" />
            <cell x="0" w="<!--{$paperWidth-$borderLeft-$borderRight}-->" align="C"><!--{$__config.list_heading}--></cell> 
            
            <!-- ########################### GENERATE TABLE HEADERS ##################################### -->
            <setfont family="Arial" style="" size="<!--{$__config.heading_font_size}-->" />
            <sety y="+10" />
            <setx x="<!--{$borderLeft}-->" />
    
            <!--{foreach name="headers" from=$columns key="number" item="columnArray"}-->
                <!--{assign var="col" value=$smarty.foreach.headers.iteration-1}-->
                <setx x="<!--{$__config.column_positions_scaled[$col]}-->" />
                <setfillcolor r="220" g="220" b="220"/>
                <cell border="1" h="5" fill="1" w="<!--{$__config.column_widths_scaled[$col]}-->" align="<!--{$__config.column_alignments[$col]}-->"><!--{$columnArray.label}--></cell>
                <setfillcolor r="255" g="255" b="255" />
            <!--{/foreach}-->
            <sety y="+5" />
            <!--<line x1="<!--{$borderLeft}-->" x2="<!--{$paperWidth-$borderRight}-->" />-->
            <!--<sety y="+2" />-->
        </allpages>
    </header>


    <footer>
        <allpages>
            <sety y="<!--{$paperHeight-$borderBottom}-->" />
            <pagenocell x="<!--{$paperWidth-$borderLeft-165}-->" w="165" align="R" label="Seite " />
        </allpages>
    </footer>


    <content>
        <addpage />
        <!-- ########################### GENERATE TABLE CONTENTS ##################################### -->
        
        <setfont family="Arial" style="" size="<!--{$__config.font_size}-->" />
        
        <!--{foreach from=$listItems item=row name="rows"}-->
            <!--{foreach name="listItems" from=$row item=value key=columnDescriptionIdentifier}-->
                <!--{assign var="col" value=$smarty.foreach.listItems.iteration-1}-->
                <setx x="<!--{$__config.column_positions_scaled[$col]}-->" />
                <cell border="1" w="<!--{$__config.column_widths_scaled[$col]}-->" align="<!--{$__config.column_alignments[$col]}-->"><!--{$value}--></cell>
            <!--{/foreach}-->
            <sety y="+5" />
        <!--{/foreach}-->
        <addpage iflessthan="<!--{$borderBottom}-->" />
        <!--<line x1="<!--{$borderLeft}-->" x2="<!--{$paperWidth-$borderRight}-->" />--> 

    </content>
</document>
