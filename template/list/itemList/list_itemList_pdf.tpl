<?xml version="1.0" encoding="UTF-8"?>
<document orientation="P" unit="mm" format="A4"><!--{strip}-->
    <!-- Main configuration -->
    <!--{assign var="borderLeft" value="20"}-->
    <!--{assign var="borderRight" value="25"}-->
    <!--{assign var="paperWidth" value="210"}-->
    <!--{assign var="paperHeight" value="297"}-->

    <!-- Table configuration -->
    <!--{assign_array var="colpos" values="20,40,140,160"}-->
    <!--{assign_array var="colwidths" values="20,100,20,25"}-->
    <!--{assign_array var="colaligns" values="C,L,R,R"}-->
    <!-- {assign_array var="tableHeadings" values="tableHeadings"|ll} -->
    <!--{/strip}-->

    <header>

    	<firstpage>
    		<addmarks />
    	</firstpage>

    	<oddpages>
    		<addmarks />
    	</oddpages>

    </header>

    <footer>

    	<allpages>

        	<setxy x="<!--{$paperWidth-$borderRight-80}-->" y="270" />
        	<cell x="<!--{$borderLeft}-->" w="165" align="C"><!-- {"page"|ll:"###CURRENTPAGENO###"} --></cell>

    	</allpages>

    </footer>

    <content>
        <addpage />
        <!-- Falz und Lochmarken -->
        <setmargins left="<!--{$borderLeft}-->" top="30" right="<!--{$borderRight}-->"/>
        <setheight h="5"/>

        <!-- Absender -->
        <setfont family="Arial" style="" size="8" />

		<ln />

		<!--{foreach from=$listItems item=row name="rows"}-->
			<!--{foreach from=$row item=value key=columnDescriptionIdentifier}-->
				<write><!--{$value}-->, </write>
			<!--{/foreach}-->
			<ln />
		<!--{/foreach}-->

    </content>
</document>
