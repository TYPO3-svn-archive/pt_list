<?xml version="1.0" encoding="UTF-8"?>
<document border_bottom="<!--{$__config.margin_bottom}-->" orientation="<!--{$__config.paper_orientation}-->" unit="mm" format="<!--{$__config.page_format}-->"><!--{strip}-->
    <!-- Main configuration -->
    <!--{assign var="borderLeft" value=$__config.margin_left}-->
    <!--{assign var="borderRight" value=$__config.margin_right}-->
    <!--{assign var="borderTop" value=$__config.margin_top}-->
    <!--{assign var="borderBottom" value=$__config.margin_bottom}-->
    <!--{assign var="paperWidth" value=$__config.page_width}-->
    <!--{assign var="paperHeight" value=$__config.page_height}-->
    <!--{/strip}-->
    
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
            <sety y="+8" />
            <!--<line x1="<!--{$borderLeft}-->" x2="<!--{$paperWidth-$borderRight}-->" />-->
            <!--<sety y="+2" />-->
        </allpages>
    </header>


    <footer>
        <allpages>
            <sety y="<!--{$paperHeight-$borderBottom}-->" />
            <cell x="<!--{$borderLeft}-->" w="165" align="L"><!--{$footerReference}--></cell>
            <pagenocell x="<!--{$paperWidth-$borderLeft-165}-->" w="165" align="R" label="Seite " />
        </allpages>
    </footer>


    <content>
        <addpage />
        <!-- ########################### GENERATE TABLE CONTENTS ##################################### -->
        
        <setfont family="Arial" style="" size="<!--{$__config.font_size}-->" />
        <setx x="<!--{$borderLeft}-->" />
        <table>
        <!--{foreach from=$listItems item=row name="rows"}-->
            <tr min_height="3.5">
            <!--{foreach name="listItems" from=$row item=value key=columnDescriptionIdentifier}-->
                <!--{assign var="col" value=$smarty.foreach.listItems.iteration-1}-->
                <td width="<!--{$__config.column_widths_scaled[$col]}-->"  min_height="3.5" align="<!--{$__config.column_alignments[$col]}-->" multi="<!--{$__config.column_multiline[$col]}-->"><!--{$value}--></td>                
            <!--{/foreach}-->
            </tr>
        <!--{/foreach}-->
        </table>
        
        <addpage iflessthan="<!--{$borderBottom}-->" />

    </content>
</document>
